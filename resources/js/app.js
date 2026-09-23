import 'flowbite';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;
document.documentElement.classList.remove('dark');
localStorage.removeItem('color-theme');

Alpine.start();
