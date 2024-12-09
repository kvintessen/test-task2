<?php

declare(strict_types=1);

namespace App\Controllers;

class Task3Controller
{
    public function handle(): void
    {
        $query = <<<SQL
SELECT 
    u.name AS user_name,
    u.phone AS user_phone,
    COALESCE(SUM(o.subtotal), 0) AS total_orders_sum,
    COALESCE(AVG(o.subtotal), 0) AS average_order_sum,
    MAX(o.created) AS last_order_date
FROM 
    users u
LEFT JOIN 
    orders o ON u.id = o.user_id
GROUP BY 
    u.id, u.name, u.phone
ORDER BY 
    total_orders_sum DESC;
SQL;
        echo "<pre>$query</pre>";
    }
}