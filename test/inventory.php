<!-- prototype palang, not sure ano kakalabasan neto -->

<!-- reference file for this prototype -->

<!-- Copy of Product List - Marine (excel file) -->
<!-- and -->
<!-- STI - IPI (COS, DR, SI Template) (excel file) (page Delivery Receipt) -->

<!-- sa page ng delivery receipt, may makikita kayo "Consisting of:" -->
<!-- some how, gusto ko siya ma pasok sa COS pag nag seselect ng product, tapos kusa narin lalabas yun. -->


<?php

require "../includes/conn.php";

$sql = "SELECT pl.product_id, pl.product_code, ln.lot_no, ln.quantity
        FROM product_list pl
        JOIN product_list_ln ln ON pl.product_code = ln.product_code_ref";

$sql_query = mysqli_query($conn, $sql);

?>

<div class="container mt-5">
        <div class="border p-3">
                <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                                <tr>
                                        <th>Product Code</th>
                                        <th>Lot Number</th>
                                        <th>Quantity</th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php
                                if (mysqli_num_rows($sql_query) > 0) {
                                        $current_product_code = null;

                                        while ($row = mysqli_fetch_assoc($sql_query)) {
                                                if ($current_product_code !== $row['product_code']) {
                                                        $current_product_code = $row['product_code'];

                                                        echo "<tr>";
                                                        echo "<td>" . htmlspecialchars($current_product_code) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['lot_no']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                                        echo "</tr>";
                                                } else {
                                                        echo "<tr>";
                                                        echo "<td></td>"; // Empty cell for product code
                                                        echo "<td>" . htmlspecialchars($row['lot_no']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                                        echo "</tr>";
                                                }
                                        }
                                } else {
                                        echo "<tr><td colspan='2'>No products found.</td></tr>";
                                }
                                ?>
                        </tbody>
                </table>
        </div>
</div>