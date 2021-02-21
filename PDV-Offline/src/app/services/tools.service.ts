import { Injectable } from "@angular/core";
import { ipcRenderer } from "electron";
import { Product } from "../tools/entities/product.entity";
import AuthService from "./auth.service";

import { MessageService } from "./message.service";


//prints
import { PrintService, UsbDriver, WebPrintDriver } from 'ng-thermal-print';
import { PrintDriver } from 'ng-thermal-print/lib/drivers/PrintDriver';
import { PaymentsForm } from "../tools/entities/payments_form.entity";

@Injectable({
	providedIn: 'root'
})
export class ToolsService {

	//prints
	status: boolean = false;
	usbPrintDriver: UsbDriver;
	webPrintDriver: WebPrintDriver;
	ip: string = '';

	tables = [
		// 'users',
		'produtos',
		'payments_forms',
		// 'users_permissions',
		// 'clientes',
	];

	constructor(
		private printService: PrintService,
		public message: MessageService,
		private service: AuthService,
	) {
		this.usbPrintDriver = new UsbDriver();
	}

	async startCarga(empresa) {
		this.message.loading(true);
		for (const table of this.tables) {
			const result = await this.service.receiveCarga(empresa.id, { table: table }).catch(err => {
				this.message.alertErro('Falha na requisição do Servidor!');
				this.message.loading();
				return false;
			});
			console.log(table, result);

			if (table == 'produtos') {
				for (let item of result) {

					const verifica = await Product.findOne(item.id);

					if (verifica) {
						await Product.update(item.id, item);
					} else {
						await Product.insert(item);
					}
				}
			}
			if (table == 'payments_forms') {
				for (let item of result) {

					const verifica = await PaymentsForm.findOne(item.id);

					if (verifica) {
						await PaymentsForm.update(item.id, item);
					} else {
						await PaymentsForm.insert(item);
					}
				}
			}

		}
		this.message.alertSuccess('Carga Finalizada!');
		this.message.loading();
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

	getSettings() {
		const settings = localStorage.getItem('settingsPDV');
		return JSON.parse(settings);
	}

	postSettings(settings) {
		settings = JSON.stringify(settings);
		localStorage.setItem('settingsPDV', settings);
	}

	async sendPrinter(printData) {
		const result = ipcRenderer.sendSync('print', printData);
		return result;
	}

	preparePrint(dados, printers) {
		// this.printService.isConnected.subscribe(result => {
		// 	this.status = result;
		// 	if (result) {
		// 		console.log('Connected to printer!!!');
		// 	} else {
		// 		console.log('Not connected to printer.');
		// 		// this.requestUsb()
		// 	}
		// });
		this.requestUsb()
	}

	requestUsb() {
		this.usbPrintDriver.requestUsb().subscribe(result => {
			console.log(result);
			// this.printService.setDriver(this.usbPrintDriver, 'ESC/POS');
		}, err => console.log(err));
	}

	// connectToWebPrint() {
	// 	this.webPrintDriver = new WebPrintDriver(this.ip);
	// 	this.printService.setDriver(this.webPrintDriver, 'WebPRNT');
	// }

	print(driver: PrintDriver) {
		this.printService.init()
			.setBold(true)
			.writeLine('Hello World!')
			.setBold(false)
			.feed(4)
			.cut('full')
			.flush();
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