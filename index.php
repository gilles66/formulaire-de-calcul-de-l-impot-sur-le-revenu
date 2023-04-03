<?php
//ini_set( 'display_errors', 1 );
//error_reporting( E_ALL );
?><!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<title>Formulaire de calcul de l'impôt sur le revenu. (France, 2022)</title>
</head>
<?php

/**
 * Returns an amount of euros formatted for french reading.
 * @param $amount
 * @return string
 */
function format_euros( $amount ) {
    return number_format($amount, 2, ',', ' ');
}

$montant_du_revenu = 0;
if ( isset( $_POST['montant_du_revenu'] ) ) {
	define( 'MAX_REVENU', 9999999999 );
	$montant_du_revenu    = $_POST['montant_du_revenu'];
//	$tableau_des_tranches_2021= [
//		[
//			'min'  => 0,
//			'max'  => 10225,
//			'taux' => 0
//		],
//		[
//			'min'  => 10226,
//			'max'  => 26070,
//			'taux' => 11
//		],
//		[
//			'min'  => 26071,
//			'max'  => 74545,
//			'taux' => 30
//		],
//		[
//			'min'  => 74546,
//			'max'  => 160336,
//			'taux' => 41
//		],
//		[
//			'min'  => 160336,
//			'max'  => MAX_REVENU,
//			'taux' => 45
//		],
//	];
	$tableau_des_tranches_2022 = [
		[
			'min'  => 0,
			'max'  => 10777,
			'taux' => 0
		],
		[
			'min'  => 10778,
			'max'  => 27478,
			'taux' => 11
		],
		[
			'min'  => 27479,
			'max'  => 78570,
			'taux' => 30
		],
		[
			'min'  => 78571,
			'max'  => 168994,
			'taux' => 41
		],
		[
			'min'  => 168995,
			'max'  => MAX_REVENU,
			'taux' => 45
		],
	];
	$tableau_des_tranches = $tableau_des_tranches_2022;
}
?>
<body>
    <div class="container mt-5 my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Formulaire de calcul de l'impôt sur le revenu.</h1>
                <div class="alert alert-primary" role="alert">
                    Les barèmes sont calculés d'après <a href="https://www.service-public.fr/particuliers/vosdroits/F1419" target="_blank">https://www.service-public.fr/particuliers/vosdroits/F1419</a>
                </div>
                <form action="?" method="post">
                    <div class="form-group" id="">
                        <label for="montant_du_revenu">Montant du revenu de l'année en euros</label>
                        <div class="input-group mb-3">
                            <input type="number" step="0.01" class="form-control" name="montant_du_revenu" id="montant_du_revenu" value="<?php echo $montant_du_revenu; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"> €</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Calculer</button>
                </form>
                <?php
                if ( isset( $_POST['montant_du_revenu'] ) ) {
	                echo '<h2 class="mt-5 mb-4">Résultat du calcul</h2>';
                    ?>
                    <table class="table">
                        <tr>
                            <th>Tranche</th>
                            <th>Montant imposable</th>
                            <th>Taux d'imposition</th>
                            <th>Imposition</th>
                        </tr>
                    <?php

                    foreach ( $tableau_des_tranches as $key => $infos ) {

                        $montant_de_la_tranche = $infos['max'] - $infos['min'];

	                    $tableau_des_tranches[$key]['imposition_de_cette_tranche'] = 0;
	                    $tableau_des_tranches[$key]['montant_imposable_de_cette_tranche'] = 0;

                        if ( $montant_du_revenu >= $infos['min'] ) {

                            $montant_imposable_de_cette_tranche = $montant_du_revenu - $infos['min'];
                            if ( $montant_imposable_de_cette_tranche > $montant_de_la_tranche ) {
	                            $montant_imposable_de_cette_tranche = $montant_de_la_tranche;
                            }
	                        $tableau_des_tranches[$key]['montant_imposable_de_cette_tranche'] = $montant_imposable_de_cette_tranche;

                            $imposition_de_cette_tranche = $montant_imposable_de_cette_tranche * ( $infos['taux'] / 100 );
	                        $tableau_des_tranches[$key]['imposition_de_cette_tranche'] = $imposition_de_cette_tranche;

                        }
                    }
                    $c = $montant_total_de_l_impot = 0;
                    foreach ( $tableau_des_tranches as $key => $infos ) {
	                    $montant_total_de_l_impot+= $infos['imposition_de_cette_tranche'];
	                    $c++;
                        ?>
                        <tr>
                            <td>
                                <?php
                                if ( $infos['max'] !== MAX_REVENU) {
	                                echo '' . format_euros( $infos['min'] ) . ' - ' . format_euros( $infos['max'] ) . ' €';
                                }
                                else {
	                                echo 'Au delà de ' . format_euros( $infos['min'] ) . ' €';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo format_euros( $infos['montant_imposable_de_cette_tranche'] ) . ' €'; ?>
                            </td>
                            <td>
                                <?php echo '<span style="color:red;">' . $infos['taux'] . '%</span>'; ?>
                            </td>
                            <td>
                                <?php echo format_euros( $infos['imposition_de_cette_tranche'] ) . ' €'; ?>
                            </td>
                        </tr>
	                    <?php
                    }
                    ?>
                        <tr>
                            <td colspan="3" class="text-right font-weight-bold">
                                Montant total de votre impôt :
                            </td>
                            <td>
			                    <?php echo '<strong>' . format_euros( $montant_total_de_l_impot ) . ' €</strong>'; ?>
                            </td>
                        </tr>
                    <?php
	                ?>
                    </table>
                    <div class="alert alert-success" role="alert">
		                <?php

		                echo 'Montant total de votre impôt : <strong>' . format_euros( $montant_total_de_l_impot ) . ' €</strong><br />';
		                echo 'Soit un taux global de  : <strong>' . format_euros( ( $montant_total_de_l_impot / $_POST['montant_du_revenu'] ) * 100 ) . ' %.</strong>';

		                ?>
                    </div>
	                <?php
                }
                ?>
            </div><!--/.col-12-->
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>








