# Inventory Management — UML & Modellierung (Phase 4)

## 1. ER-Diagramm

Entspricht dem Schema in `db/schema.sql`.

```mermaid
erDiagram
    categories ||--o{ items : "has"
    items ||--o{ stock_movements : "has"

    categories {
        int id PK
        varchar name
        varchar description
    }
    items {
        int id PK
        int category_id FK
        varchar name
        varchar sku UK
        varchar description
        timestamp created_at
        timestamp updated_at
    }
    stock_movements {
        int id PK
        int item_id FK
        enum type
        int quantity
        varchar note
        timestamp created_at
        timestamp updated_at
    }
```

## 2. Use Case Diagram

Zwei Akteure. **Admin** ist ein **Lagermitarbeiter** mit zusätzlichen Rechten
(Generalisierung), kann also alles vom Lagermitarbeiter plus Verwaltung.

```mermaid
graph LR
    Admin([👤 Admin]) -->|is a| Lager([👤 Lagermitarbeiter])

    Lager --> UC1(("Wareneingang buchen"))
    Lager --> UC2(("Warenausgang buchen"))
    Lager --> UC3(("Bestand ansehen"))

    Admin --> UC4(("Artikel verwalten"))
    Admin --> UC5(("Kategorien verwalten"))
```

## 3. Class Diagram (Domain Model)

```mermaid
classDiagram
    class Category {
        +int id
        +string name
        +string description
    }
    class Item {
        +int id
        +int category_id
        +string sku
        +string name
        +string description
        +string created_at
        +string updated_at
        +getCurrentStock()
    }
    class StockMovement {
        +int id
        +int item_id
        +int quantity
        +string note
        +string type
        +string created_at
        +string updated_at
        +getSignedQuantity()
        +getItem()
    }
    Category "1" --> "0..*" Item : has
    Item "1" --> "0..*" StockMovement : has
```

## 4. Activity Diagram — „Warenausgang buchen"

Der Entscheidungspunkt `{Menge ≤ Bestand?}` ist die Business-Regel aus Phase 2
(kein negativer Bestand), hier im Ablauf sichtbar gemacht.

```mermaid
flowchart TD
    Start([Start]) --> A[Artikel & Menge wählen]
    A --> B[aktuellen Bestand berechnen]
    B --> D{Menge ≤ Bestand?}
    D -->|Ja| C[Bewegung speichern: type = out]
    D -->|Nein| E[Fehlermeldung: nicht genug Bestand]
    C --> Ende([Ende])
    E --> Ende
```


flowchart TD
    Start([Start]) --> A[Artikel & Menge wählen]
    A --> D{aktuellen Bestand berechnen} → {Menge ≤ Bestand?}
    D -->|Ja| B[Bewegung speichern: type = out]
    D -->|Nein| C[Fehlermeldung: nicht genug Bestand] → (zurück oder Ende)
    B --> Ende([Ende])
    C --> Ende
