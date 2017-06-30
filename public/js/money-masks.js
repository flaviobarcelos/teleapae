$(function(){
	$(".mask-dolar").maskMoney()
	$(".mask-real").maskMoney({allowZero:"true",symbol:"R$",decimal:",",thousands:"."});
	$(".mask-euro").maskMoney({symbol:"Euro",decimal:",",thousands:" "});
	$(".mask-precision").maskMoney({symbol:"", decimal:"",thousands:"", precision:0.1});
	$(".real-number").maskMoney({symbol:"", decimal:".",thousands:""});
	$(".mask-porcentagem").maskMoney({symbol:"", decimal:".",thousands:"", precision:4});
});