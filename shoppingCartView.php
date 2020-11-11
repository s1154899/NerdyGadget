<?php
include_once __DIR__ . "/Class/SessionHandeler.php";
include_once __DIR__ . "/Class/ShoppingCartHandeler.php";

$shopping = new ShoppingCartHandeler();

$Connection = mysqli_connect("localhost", "root", "", "nerdygadgets");
mysqli_set_charset($Connection, 'latin1');



?>
<head>
    <link rel="stylesheet" href="Public/CSS/Style.css">
    <script>
        function SwitchCartState(){
            var cart = document.getElementById("shoppingCart");
            if (cart.getAttribute("class") == "shoppingCartHidden") {
                cart.setAttribute("class", "shoppingCartDisplay");
            }else{
                cart.setAttribute("class", "shoppingCartHidden");
            }
        }
    </script>
</head>
<a onclick="SwitchCartState()">
    <div style="height: 5vw;width: 5vw; background-color: green; position: fixed;left: 10vw;z-index: 10">

    </div>
</a>
<div id="shoppingCart" class="shoppingCartHidden">
    <div id="shoppingFrame">
        <?php

        foreach ($shopping->GetProducts() as $product) {

            $Query = "
                SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, ROUND(TaxRate * RecommendedRetailPrice / 100 + RecommendedRetailPrice,2) as SellPrice,
            
                (SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
                (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                WHERE SI.StockItemID = ?
               ";


            $Statement = mysqli_prepare($Connection, $Query);
            mysqli_stmt_bind_param($Statement, "i", $product["productId"]);
            mysqli_stmt_execute($Statement);
            $ReturnableResult = mysqli_stmt_get_result($Statement);
            $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);

            ?>
            <div class="shoppingProduct">
                <div class="shoppingCartFoto">
                    <?php echo "<img src='"."/NerdyGadgets/Public/StockItemIMG/".$ReturnableResult[0]["ImagePath"]."'>";?>
                </div>
                <div class="shoppingCartName">
                    <?php
                    echo "<p>".$ReturnableResult[0]["StockItemName"]."</p>";
                    ?>
                </div>
                <div class="shoppingPrice">
                    <?php echo "<p>€".$ReturnableResult[0]["SellPrice"]."</p>"; ?>
                </div>
                <div class="shoppingQuantity">
                    <?php echo "<p>".$product["quantity"]."</p>" ?>
                </div>
                <div class="shoppingAddRemove">
                    <?php ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div>
        <button>verder winkelen</button>
        <button>betalen</button>
    </div>
</div>
