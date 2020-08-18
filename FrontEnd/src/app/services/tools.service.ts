import { Injectable } from "@angular/core";

@Injectable({
	providedIn: 'root'
})
export class ToolsService {
	screen = window.innerHeight;

	getScreen() {
		let tela = this.screen * 0.5;
		console.log(tela);
		return tela;
	}

	money(n, c = 2, d = ',', t = '.') {
		let s: any; let i: any; let j: any;

		c = isNaN(c = Math.abs(c)) ? 2 : c;
		d = d === undefined ? ',' : d;
		t = t === undefined ? '.' : t;
		s = n < 0 ? '-' : '';
		// tslint:disable-next-line: radix
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '';
		j = (j = i.length) > 3 ? j % 3 : 0;
		// tslint:disable-next-line: max-line-length
		return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
	}

	nova_data(i = 0) {
		const data = new Date();

		let dia: any = data.getDate();
		let mes: any = data.getMonth() + 1 + i;
		let ano = data.getFullYear();

		if (mes >= 13) { //se passar do mês 12, então inicia com o mes 1 do próximo ano
			mes = 1;
			ano = ano + 1;
		}

		if (mes == 2 && dia > 28) {
			dia = 28;
		}

		if (mes == 4 && dia > 30 || mes == 6 && dia > 30 || mes == 9 && dia > 30 || mes == 11 && dia > 30) {
			dia = 30;
		}

		dia = (dia < 10) ? '0' + dia : dia;
		mes = (mes < 10) ? '0' + mes : mes;

		return ano + '-' + mes + '-' + dia;
	}

}
