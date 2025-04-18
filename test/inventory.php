<!-- prototype palang, not sure ano kakalabasan neto -->

<!-- reference file for this prototype -->

<!-- Copy of Product List - Marine (excel file) -->
<!-- and -->
<!-- STI - IPI (COS, DR, SI Template) (excel file) (page Delivery Receipt) -->

<!-- sa page ng delivery receipt, may makikita kayo "Consisting of:" -->
<!-- some how, gusto ko siya ma pasok sa COS pag nag seselect ng product, tapos kusa narin lalabas yun. -->

<?php

require "../includes/conn.php";

$sql = "SELECT pl.product_id, pl.product_code, ln.lot_no, ln.no_of_cans, ln.pack_size, ln.liters, ln.description
        FROM product_list pl
        JOIN product_list_ln ln ON pl.product_code = ln.product_code_ref
        ORDER BY pl.product_code, ln.lot_no";

$sql_query = mysqli_query($conn, $sql);

?>

<div class="container mt-5">
        <div class="border p-3">
                <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                                <tr>
                                        <th>Product Code</th>
                                        <th>Lot Number</th>
                                        <th>No of Cans</th>
                                        <th>Pack Size</th>
                                        <th>Liters</th>
                                        <th>Description</th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php
                                if (mysqli_num_rows($sql_query) > 0) {
                                        $current_product_code = null;

                                        while ($row = mysqli_fetch_assoc($sql_query)) {
                                                echo "<tr>";

                                                if ($current_product_code !== $row['product_code']) {
                                                        $current_product_code = $row['product_code'];
                                                        echo "<td>" . htmlspecialchars($current_product_code) . "</td>";
                                                } else {
                                                        echo "<td></td>";
                                                }

                                                echo "<td>" . htmlspecialchars($row['lot_no']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['no_of_cans']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['pack_size']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['liters']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                                echo "</tr>";
                                        }
                                } else {
                                        echo "<tr><td colspan='3'>No products found.</td></tr>";
                                }
                                ?>
                        </tbody>
                </table>
        </div>
</div>