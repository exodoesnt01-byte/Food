<?php
class Food
{
    public string $name;
    public string $category;
    public string $cuisine;
    public float $price;
    public int $quantity;
    public ?string $expirationDate;

    public function __construct(
        string $name,
        string $category,
        string $cuisine,
        float $price,
        int $quantity,
        ?string $expirationDate
    ) {
        $this->name = $name;
        $this->category = $category;
        $this->cuisine = $cuisine;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->expirationDate = $expirationDate;
    }

    public function insert(mysqli $connection): int
    {
        $statement = $connection->prepare(
            'INSERT INTO foods (name, category, cuisine, price, quantity, expiration_date)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->bind_param(
            'sssdis',
            $this->name,
            $this->category,
            $this->cuisine,
            $this->price,
            $this->quantity,
            $this->expirationDate
        );
        $statement->execute();
        $id = (int) $connection->insert_id;
        $statement->close();

        return $id;
    }

    public static function getAll(mysqli $connection): array
    {
        $result = $connection->query(
            'SELECT id, name, category, cuisine, price, quantity, expiration_date
             FROM foods ORDER BY id DESC'
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
