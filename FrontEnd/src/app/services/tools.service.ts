import { Injectable } from "@angular/core";
import { MessageService } from './message.service';

declare var qz;
// import * as qz from 'qz-tray';
import { sha256 } from 'js-sha256';
import { KJUR, KEYUTIL, stob64, hextorstr } from 'jsrsasign';

@Injectable({
	providedIn: 'root'
})
export class ToolsService {
	screen = window.innerHeight;
	qzStart: boolean = false;

	constructor(
		private message: MessageService
	) {
		this.qzInit();
	}

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

	async qzInit() {
		await qz.security.setCertificatePromise(async (resolve, reject) => {
			await fetch("assets/pluginQZ/digital-certificate.txt", { cache: 'no-store', headers: { 'Content-Type': 'text/plain' } })
				.then(data => resolve(data.text()));
		});

		console.log('setCertificatePromise');

		await qz.security.setSignaturePromise(hash => {
			return async (resolve, reject) => {
				await fetch("assets/pluginQZ/private-key.pem", { cache: 'no-store', headers: { 'Content-Type': 'text/plain' } })
					.then(wrapped => wrapped.text())
					.then(data => {

						var pk = KEYUTIL.getKey(data);
						var sig = new KJUR.crypto.Signature({ "alg": "SHA1withRSA" });
						sig.init(pk);
						sig.updateString(hash);
						var hex = sig.sign();
						// console.log("DEBUG: \n\n" + stob64(hextorstr(hex)));

						resolve(stob64(hextorstr(hex)));
					})
					.catch(err => console.error(err));
			};
		});

		console.log('setSignaturePromise');

		await qz.api.setSha256Type(data => sha256(data));
		await qz.api.setPromiseType(resolver => new Promise(resolver));

		await qz.websocket.connect().then(() => {
			this.qzStart = true;
			// this.listPrints();
		}).catch(() => {
			this.message.alertErro('Não foi possível uma conexão com o plugin QZ!', 'Falha no Plugin');
		});
		return this.qzStart;
	}

	async listPrints() {
		if (!await this.verificaQz()) {
			return;
		}
		const printers = await qz.printers.find();
		console.log(printers);
		return printers;
	}

	async verificaQz() {
		if (!this.qzStart) {
			await this.qzInit();
		}
		console.log(this.qzStart);
		return this.qzStart;
	}

	async send_print(data) {
		const printer = localStorage.getItem('printOne');

		if (printer == null || printer == undefined) {
			await this.config_print(data);
		} else {
			await this.printing(data, printer);
		}
	}

	async printing(base64, printer) {
		console.log(printer, base64);
		const config = await qz.configs.create(printer);

		const data = [
			{
				type: 'raw',
				format: 'base64',
				data: base64
			}
		];

		await qz.print(config, data).then(() => localStorage.setItem('printOne', printer))
			.catch((e) => { this.message.alertErro('Falha ao imprimir: ' + JSON.stringify(e)) });
	}

	async config_print(data) {
		const printers = await this.listPrints();

		const { value: printer } = await this.message.swal.fire({
			title: 'Configurar Impressora',
			html: 'Você ainda não configurou uma impressora, selecione uma opção para configurar!<br>',
			input: 'select',
			customClass: {
				input: 'form-control',
			},
			inputOptions: printers,
			inputPlaceholder: 'Selecione uma Impressora',
			showCancelButton: true,
			cancelButtonText: 'Voltar',
		})

		if (printer) {
			await this.printing(data, printers[printer]);
			// this.message.swal.fire(`You selected: ${printers[printer]}`)
		}
	}

}
