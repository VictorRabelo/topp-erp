import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Hotkey, HotkeysService } from 'angular2-hotkeys';
import { DatabaseService } from "./tools/database.service";

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ModalLoginComponent } from './shared/modal-login/modal-login.component';
import { MessageService } from './services/message.service';
import { Product } from './tools/entities/product.entity';
import { Like } from 'typeorm';
import { ModalSettingsComponent } from './shared/modal-settings/modal-settings.component';
import { ToolsService } from './services/tools.service';
import { ModalProductsComponent } from './shared/modal-products/modal-products.component';
import { VendaItens } from './tools/entities/venda_itens.entity';
import { Venda } from './tools/entities/venda.entity';
import { Empresa } from './tools/entities/empresa.entity';
import { VendaPayments } from './tools/entities/venda_payments.entity';
import { ModalVendasComponent } from './shared/modal-vendas/modal-vendas.component';
import { ipcRenderer } from 'electron';
import { ModalCheckoutComponent } from './shared/modal-checkout/modal-checkout.component';
import { ModalDescontoComponent } from './shared/modal-desconto/modal-desconto.component';


@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {

    @ViewChild('inputProduct', { static: true }) inputProduct: ElementRef;

    screenHeight = window.innerHeight;
    labelDisplay = "Caixa Livre";

    user: any = {};
    settings: any = {};
    empresa: any = {};

    vendaCurrent: any = { total: 0 };
    itens: any = [];
    payments: any = [];

    intervalTime: any = null;

    constructor(
        private modalCtrl: NgbModal,
        private message: MessageService,
        private databaseService: DatabaseService,
        private tools: ToolsService,
        private hotkey: HotkeysService,
    ) {
        // console.log(this.screenHeight);
        // for (let i = 1; i <= 15; i++) {
        //     this.itens.push({ item: i, description: `Produto Teste ${i}`, quantidade: 1, valor_unit: 10, desconto: 5, total: 5 });
        // }

        this.hotkey.add(new Hotkey('f2', (event: KeyboardEvent): boolean => {
            this.startSale();
            return false; // Prevent bubbling
        }));
        this.hotkey.add(new Hotkey('f3', (event: KeyboardEvent): boolean => {
            this.open_checkout();
            return false; // Prevent bubbling
        }));
        this.hotkey.add(new Hotkey('f4', (event: KeyboardEvent): boolean => {
            this.cancel_item();
            return false; // Prevent bubbling
        }));
        this.hotkey.add(new Hotkey('f5', (event: KeyboardEvent): boolean => {
            this.cancel_venda();
            return false; // Prevent bubbling
        }));
        this.hotkey.add(new Hotkey('f6', (event: KeyboardEvent): boolean => {
            this.open_products();
            return false; // Prevent bubbling
        }));
        this.hotkey.add(new Hotkey('f7', (event: KeyboardEvent): boolean => {
            this.add_cliente();
            return false; // Prevent bubbling
        }));
        this.hotkey.add(new Hotkey('f10', (event: KeyboardEvent): boolean => {
            // console.log('Typed hotkey');
            this.open_sales();
            return false; // Prevent bubbling
        }));
        this.hotkey.add(new Hotkey('f11', (event: KeyboardEvent): boolean => {
            // console.log('Typed hotkey');
            this.confirm_restart();
            return false; // Prevent bubbling
        }));

        this.hotkey.add(new Hotkey('ctrl + d', (event: KeyboardEvent): boolean => {
            this.open_desconto();
            return false; // Prevent bubbling
        }));
    }

    confirm_restart() {
        if (!this.vendaCurrent.id) {
            return;
        }
        this.message.swal.fire({
            title: 'Liberar Caixa',
            html: `Deseja liberar o caixa ?`,
            icon: 'question',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) {
                this.restart();
            }
        })
    }

    restart() {
        this.labelDisplay = "Caixa Livre";
        this.vendaCurrent = { total: 0 };
        this.itens = [];
        this.payments = [];
        this.inputProduct.nativeElement.focus();
    }

    //  ========================================================================================================================
    async ngOnInit() {
        this.intervalTime = setInterval(this.currentTime, 500);
        if (this.tools.getSettings() != null && this.tools.getSettings() != undefined) {
            this.settings = this.tools.getSettings();
        }

        this.message.loading();
        this.checkLogin();
    }

    //vendas ========================================================================================================================
    async startSale() {
        let venda: any = await Venda.insert({ empresa_id: this.empresa.id, user_id: this.user.id }).catch(err => {
            this.message.alertErro(err);
            return;
        });

        await this.getVenda(venda.raw);
    }
    async getVenda(venda_id) {
        const venda = await Venda.findOne(venda_id).catch(err => {
            this.message.alertErro(err);
            return;
        });

        await this.getItens(venda_id);

        const payments = await VendaPayments.find({ where: { venda_id: venda_id } }).catch(err => {
            this.message.alertErro(err);
            return;
        });

        this.vendaCurrent = venda;
        this.payments = payments;
        this.labelDisplay = 'venda aberta';

        await this.calc_sale();
    }
    async calc_sale() {
        let subtotal = 0;
        let desconto = 0;
        console.log(this.itens);
        for (const item of this.itens) {
            subtotal += parseFloat(item.quantidade) * parseFloat(item.valor_unitario);
            desconto += parseFloat(item.desconto)
        }

        let total = subtotal - desconto;

        this.vendaCurrent.subtotal = subtotal;
        this.vendaCurrent.total = total;
        console.log(this.vendaCurrent);
        await Venda.update(this.vendaCurrent.id, this.vendaCurrent);
    }
    cancel_venda() {
        if (!this.vendaCurrent.id) {
            // this.labelDisplay = "Venda Fechada!";
            // this.message.alertErro('Venda não inicializada!');
            return false;
        }

        this.message.swal.fire({
            title: 'Cancelar Venda ?',
            html: `Deseja realmente cancelar a venda ?`,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            showCancelButton: true
        }).then(async (res) => {
            if (res.isConfirmed) {
                await Venda.update(this.vendaCurrent.id, { status: 0 });
                this.restart();
            }
        })
    }
    open_sales() {
        const modal = this.modalCtrl.open(ModalVendasComponent, { size: 'xl', centered: true, backdrop: 'static' });
        modal.componentInstance.data = this.empresa;
        modal.result.then(res => {
            if (res) {
                this.getVenda(res.toString());
                this.inputProduct.nativeElement.focus();
            }
        })
    }

    ///itens ========================================================================================================================
    async getItens(venda_id) {
        const itens = await VendaItens.find({ where: { venda_id: venda_id } }).catch(err => {
            this.message.alertErro(err);
            return;
        });
        this.itens = itens;
    }
    async searchProduct(value: string) {

        if (this.settings.startSale == 0 && !this.vendaCurrent.id) {
            this.labelDisplay = "Venda Fechada!";
            this.message.alertErro('Venda não inicializada!');
            return false;
        }

        const pos = value.indexOf('*');
        let quantidade = '1';
        if (pos > 0) {
            quantidade = value.slice(0, pos);
        }

        const codigo = value.slice(pos + 1);
        console.log(pos, quantidade, codigo);

        const products = await Product.find({
            where: [
                { id: Like(`${codigo}`) },
                { codigo_barras: Like(`${codigo}`) },
                { referencia: Like(`${codigo}`) }
            ]
        });
        if (products.length == 1) { // produto encontrado


            if (this.vendaCurrent.id == undefined) {
                await this.startSale();
            }

            console.log('vendaCurrent', this.vendaCurrent);
            console.log('products', products);

            const product = products[0];

            const item: any = {
                venda_id: this.vendaCurrent.id,
                produto_id: product.id,
                descricao: product.descricao,
                quantidade: quantidade,
                valor_unitario: product.preco,
                desconto: 0,
                total: parseFloat(quantidade) * parseFloat(product.preco)
            }

            await VendaItens.insert(item);

            this.labelDisplay = product.descricao;

            await this.getItens(this.vendaCurrent.id);

            await this.calc_sale();

            this.inputProduct.nativeElement.value = "";
            this.inputProduct.nativeElement.focus();

        } else if (products.length > 1) { //mais de 1 produto encontrado
            console.log('Vários produtos encontrados!');
            this.open_products();
        } else {//nenhum produto encontrado
            console.log('Nenhum produto encontrado!');
            this.open_products();
        }
    }
    cancel_item() {
        if (!this.vendaCurrent.id && this.itens.length == 0) {
            // this.labelDisplay = "Venda Fechada!";
            // this.message.alertErro('Venda não inicializada!');
            return false;
        }

        this.message.swal.fire({
            title: 'Cancelar Item',
            html: `Informe o número do item para cancelar!`,
            input: 'number',
            width: '25rem',
            customClass: { input: 'form-control' },
            showConfirmButton: false,
        }).then(async (res) => {
            if (res.isConfirmed) {
                if (res.value > 0) {

                    const index = res.value - 1;
                    const item = this.itens[index];

                    if (item) {
                        await VendaItens.delete(item.id);
                        this.getVenda(this.vendaCurrent.id);
                    }

                    console.log(item);

                } else {
                    this.message.alertErro("informe o item para cancelar!");
                }
            }
        })
    }
    open_products() {
        const modal = this.modalCtrl.open(ModalProductsComponent, { size: 'xl', centered: true, backdrop: 'static' });
        modal.componentInstance.data = this.inputProduct.nativeElement.value;
        modal.result.then(res => {
            console.log(res);
            if (res) {
                this.inputProduct.nativeElement.value = res;
                // this.searchProduct(res.toString());
            }
        })
    }

    ///cliente ========================================================================================================================
    add_cliente() {
        if (!this.vendaCurrent.id) {
            // this.labelDisplay = "Venda Fechada!";
            // this.message.alertErro('Venda não inicializada!');
            return false;
        }

        this.message.swal.mixin({
            input: 'text',
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Voltar',
            showCancelButton: true,
            progressSteps: ['1', '2']
        }).queue([
            {
                title: 'Informe o NOME ou RAZÃO.',
            },
            {
                title: 'Informe o CPF ou CNPJ.',
            },
        ]).then(async (result: any) => {
            const res = result.value;
            if (res) {
                if (res[1] !== "") {//nome cliente
                    this.vendaCurrent.cpf = res[1];
                }
                if (res[0] !== "") {//nome cliente
                    this.vendaCurrent.cliente = res[0];
                    await Venda.update(this.vendaCurrent.id, this.vendaCurrent);
                }
            }
            console.log(result);
        });
    }

    //settings ========================================================================================================================
    open_settings() {
        const modal = this.modalCtrl.open(ModalSettingsComponent, { size: 'md', centered: true, backdrop: 'static' });
        modal.componentInstance.data = this.user;
        modal.result.then(res => {
            console.log(res);
            if (res) {
                this.settings = res;
            }
        })
    }


    //login ========================================================================================================================
    checkLogin() {
        if (!this.user.id) {
            this.open_login();
        }
    }

    open_login() {
        const modal = this.modalCtrl.open(ModalLoginComponent, { size: 'sm', centered: true, backdrop: 'static' });
        modal.componentInstance.data = this.user;
        modal.result.then(async (res) => {
            console.log(res);
            if (res) {
                this.user = res;
                this.empresa = await Empresa.findOne(this.user.empresa_id);
                setTimeout(() => this.inputProduct.nativeElement.focus(), 300)
            }
        })
    }

    //Checkout ===========================================================================================================================
    open_desconto() {
        if (!this.vendaCurrent.id) {
            return false;
        }
        const modalRef = this.modalCtrl.open(ModalDescontoComponent, { size: 'sm', centered: true, backdrop: 'static' });
        modalRef.componentInstance.data = this.vendaCurrent;
        modalRef.result.then(res => {
            if (res) {
                this.vendaCurrent = res;
            }
        })
    }
    open_checkout() {
        if (!this.vendaCurrent.id) {
            return false;
        }
        const modal = this.modalCtrl.open(ModalCheckoutComponent, { size: 'md', centered: true, backdrop: 'static' });
        modal.componentInstance.data = this.vendaCurrent;
        modal.result.then(async (res) => {
            console.log(res);
            if (res) {
                this.restart();
            }
        })
    }


    //tools ========================================================================================================================
    currentTime() {
        const data = new Date();
        let dia: any = data.getDate();
        let mes: any = data.getMonth() + 1;
        let ano = data.getFullYear();

        let hora: any = data.getHours();
        let min: any = data.getMinutes();
        let sec: any = data.getSeconds();

        if (dia < 10) {
            dia = `0${dia}`;
        }
        if (mes < 10) {
            mes = `0${mes}`;
        }

        if (hora < 10) {
            hora = `0${hora}`;
        }
        if (min < 10) {
            min = `0${min}`;
        }
        if (sec < 10) {
            sec = `0${sec}`;
        }

        return `${dia}/${mes}/${ano} ${hora}:${min}:${sec}`;
    }

    testPrint() {
        // const cupom = this.tools.prepareCupom();

        const printData = { printer: this.settings.printer };

        // console.log(this.tools.sendPrinter(printData));
        this.tools.preparePrint([], this.settings.printer);
    }

    shortCode(event: KeyboardEvent) {
        console.log(event);
        if (event.key === "F2") {
            this.startSale();
        }
        if (event.key === "F3") {
            this.open_checkout();
        }
        if (event.key === "F4") {
            this.cancel_item();
        }
        if (event.key === "F5") {
            this.cancel_venda();
        }
        if (event.key === "F6") {
            this.open_products();
        }
        if (event.key === "F7") {
            this.add_cliente();
        }
        if (event.key === "F10") {
            this.open_sales();
        }
        if (event.key === "F11") {
            this.confirm_restart();
        }
    }

    receiveCloud() {
        this.message.swal.fire({
            title: 'Iniciar Sincronização Cloud ?',
            icon: 'question',
            html: `Deseja buscar os dados do servidor para este terminal ?`,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Voltar',
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) {
                console.log(this.empresa);
                this.tools.startCarga(this.empresa);
            }
        });
    }

    exit() {
        this.message.swal.fire({
            title: 'Deseja sair do PDV ?',
            icon: 'question',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) {
                ipcRenderer.send('exit');
            }
        })
    }

}
