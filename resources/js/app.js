import './bootstrap';
import Chart from 'chart.js/auto';
import Swal from 'sweetalert2';

window.Chart = Chart;
window.loadJsPdf = function () {
	return import('jspdf').then(function (module) {
		return module.jsPDF;
	});
};
window.Swal = Swal;
