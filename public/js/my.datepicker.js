jQuery(function($){
	$.datepicker.regional['pt-BR'] = {
		closeText: 'Fechar',
		prevText: '&#x3c;Anterior',
		nextText: 'Pr&oacute;ximo&#x3e;',
		currentText: 'Hoje',
		monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
		'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
		'Jul','Ago','Set','Out','Nov','Dez'],
		dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dateFormat: 'dd/mm/yy', firstDay: 0,
		isRTL: false};
		$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
});

$(function() {
	$("#datepicker").datepicker({showOn: 'button', buttonImage: 'public/img/icons/calendar.gif',
	changeMonth: true,
	changeYear: true,
	buttonImageOnly: true});
});

$(function() {
	$("#datepicker2").datepicker({showOn: 'button', buttonImage: 'public/img/icons/calendar.gif',
	changeMonth: true,
	changeYear: true,
	buttonImageOnly: true});
});

$(function() {
	$(".datepicker").datepicker({showOn: 'button', buttonImage: 'public/img/icons/calendar.gif',
	changeMonth: true,
	changeYear: true,
	buttonImageOnly: true});
});