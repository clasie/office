$(document).ready(function() {

       //calcul beton
       $("#" + "type_btn_beton_equal").click(function(){

		var type_beton_metre_carre = parseFloat($("#" + "type_beton_metre_carre").val());
		var type_beton_metre = parseFloat($("#" + "type_beton_metre").val());
	        var selected =  $("#" + "type_beton_select").find('option:selected');
	        var prix = parseFloat(selected.data('prix')); 
                var tot = prix * type_beton_metre_carre * type_beton_metre;
                $("#type_beton_prix").val(tot);
       });
       //calcul beton feraillage
       $("#" + "type_btn_beton_feraillage_equal").click(function(){

		var type_beton_metre_carre = parseFloat($("#" + "type_beton_feraillage_metre_carre").val());
	        var selected =  $("#" + "type_beton_feraillage_select").find('option:selected');
	        var prix = parseFloat(selected.data('prix')); 
                var tot = prix * type_beton_metre_carre;
                $("#type_beton_feraillage_prix").val(tot);
       });
       //calcul total
       $("#" + "type_btn_total").click(function(){

		var type_beton_prix= parseFloat($("#" + "type_beton_prix").val());
                var type_beton_feraillage_prix = parseFloat($("#" + "type_beton_feraillage_prix").val());

                var tot = type_beton_feraillage_prix + type_beton_prix;
                $("#type_total_prix").val(tot);
       });
});
