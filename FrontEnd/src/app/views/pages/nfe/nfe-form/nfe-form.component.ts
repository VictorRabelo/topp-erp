import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { NFeService } from '../../../../services/nfe.service';
import { ToolsService } from '../../../../services/tools.service';
import { PaymentService } from '../../../../services/payment.service';
import { ClienteSearchComponent } from '../../vendas/modais/cliente-search/cliente-search.component';
import { ProdutoSearchComponent } from '../../vendas/modais/produto-search/produto-search.component';
import { ProdutoDetalheComponent } from '../../vendas/modais/produto-detalhe/produto-detalhe.component';
import { GeraNotaComponent } from '../../vendas/modais/gera-nota/gera-nota.component';


@Component({
   selector: 'kt-nfe-form',
   templateUrl: './nfe-form.component.html',
   styleUrls: ['./nfe-form.component.scss']
})
export class NfeFormComponent implements OnInit {

   loading: boolean = false;
   loadingItens: boolean = false;

   nfe: any = {};
   ItemSource = [];
   paymentSource = [];

   paymentSelect = [];

   payment: string = "";

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: NFeService,
      private servicePayment: PaymentService,
      private util: ToolsService,
      private ActiveRoute: ActivatedRoute,
      private modalCtrl: NgbModal,
      private router: Router
   ) {
      this.getPayments();
   }

   ngOnInit() {
      this.loading = true;
      this.ActiveRoute.params.subscribe(params => {
         if (params['id']) {
            const id = params['id'];
            this.getDados(id);
            this.getItens(id);
         }
         else {
            this.close();
         }
      });
   }

   close() {
      this.router.navigate(['/nfe']);
   }

   getPayments() {
      this.loading = true;
      this.servicePayment.getList({}).subscribe(resp => {
         this.paymentSource = resp;
         this.loading = false;
         this.ref.detectChanges();
      }, (erro) => {
         this.close();
      });
   }

   getDados(id) {
      this.loading = true;
      this.service.getById(id).subscribe((resp: any) => {
         this.loading = false;

         this.nfe = resp;

         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   getItens(id_nfe) {
      this.loadingItens = true;
      this.service.getListItens({ nfe_id: id_nfe }).subscribe((resp: any) => {
         this.ItemSource = resp.itens;
         this.nfe.subtotal = resp.totais.subtotal;
         this.nfe.total = resp.totais.total;
         this.paymentSelect = resp.payments;
         this.payments_calc_resto();
         this.loadingItens = false;
         this.ref.detectChanges();
      }, (erro) => {
         this.loadingItens = false;
         this.ref.detectChanges();
      });
   }


   //Cliente
   add_cliente() {
      if (this.nfe.cstatus == 100 || this.nfe.cstatus == 101 || this.nfe.cstatus == 135 || this.nfe.cstatus == 155) {
         return false;
      }

      const modalRef = this.modalCtrl.open(ClienteSearchComponent, { size: 'lg', backdrop: 'static' });
      modalRef.componentInstance.data = { nfe: this.nfe };
      modalRef.result.then(resp => {
         if (resp) {
            this.nfe.cliente_id = resp.id;
            this.nfe.razao = resp.razao;
            this.nfe.fantasia = resp.fantasia;
            this.nfe.cnpj = resp.cnpj;
            this.nfe.inscricao_estadual = resp.inscricao_estadual;
            this.nfe.logradouro = resp.logradouro;
            this.nfe.numero = resp.numero;
            this.nfe.bairro = resp.bairro;
            this.nfe.complemento = resp.complemento;
            this.nfe.cidade = resp.cidade;
            this.nfe.uf = resp.uf;
            this.nfe.ibge = resp.ibge;
            this.ref.detectChanges();
         }
      });
   }

   //Produtos
   search_item() {
      const modalRef = this.modalCtrl.open(ProdutoSearchComponent, { size: 'xl', backdrop: 'static' });
      modalRef.result.then(resp => {
         if (resp) {
            console.log(resp);
            this.open_item(resp);
         }
      })
   }

   open_item(item) {
      const modalRef = this.modalCtrl.open(ProdutoDetalheComponent, { size: 'lg', backdrop: 'static' });
      modalRef.componentInstance.data = { 'item': item, 'venda': this.nfe };
      modalRef.result.then(resp => {
         if (resp) {
            resp.nfe_id = this.nfe.id;

            console.log(resp);
            if (resp.id) {
               this.update_item(resp);
            } else {
               this.add_item(resp);
            }
         }
      });
   }

   add_item(item) {
      this.loading = true;
      this.service.create_item(item).subscribe(resp => {
         this.loading = false;
         this.ref.detectChanges();
         this.getItens(this.nfe.id);
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }
   update_item(item) {
      this.loading = true;
      this.service.update_item(item, item.id).subscribe(resp => {
         this.loading = false;
         this.ref.detectChanges();
         this.getItens(this.nfe.id);
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   del_item_confirm(item) {
      this.message.swal.fire({
         // title: 'Remover ?',
         icon: 'question',
         html: `Desaja remover o item: <br><b>${item.descricao}</b> ?`,
         confirmButtonText: 'Confirmar',
         showCancelButton: true,
         cancelButtonText: 'Não',
      }).then((result) => {
         if (!result.dismiss) {
            this.delete_item(item);
         }
      });
   }

   delete_item(item) {
      if (this.nfe.cstatus == 100 || this.nfe.cstatus == 101 || this.nfe.cstatus == 135 || this.nfe.cstatus == 155) {
         return false;
      }

      this.loadingItens = true;
      this.ref.detectChanges();
      this.service.delete_item(item.id).subscribe((resp: any) => {
         this.getItens(this.nfe.id);
      }, erro => {
         this.loadingItens = false;
         this.ref.detectChanges();
      });
   }


   //pagamentos
   change_payment(ev) {
      const id_pay = ev.target.value;

      const indice = this.paymentSource.findIndex(pay => pay.id == id_pay);

      const paymentSeleted = this.paymentSource[indice];

      const payment = {
         'id': paymentSeleted.id,
         'forma': paymentSeleted.forma,
         'max_parcelas': paymentSeleted.max_parcelas,
         'obs_title': paymentSeleted.obs,
         'valor': 0
      }

      this.message.swal.fire({
         title: payment.forma,
         html: `Falta pagar: R$ ${this.util.money(this.nfe.resto)}`,
         input: 'number',
         inputValue: this.nfe.resto,
         inputAttributes: {
            autocapitalize: 'off',
            'placeholder': 'Informe o valor pago...',
            'step': 'any'
         },
         showCancelButton: true,
         cancelButtonText: 'Voltar',
         confirmButtonText: 'Confirmar',
         customClass: { popup: 'swal2-sm' }
      }).then(result => {
         this.payment = '';

         if (result.value && result.value != '') {
            const valor = parseFloat(result.value);

            //verifica se pode inserir mais do que o valor a ser pago
            if (paymentSeleted.more == 0 && valor > this.nfe.resto) {
               this.message.alertErro('Pagamento acima do total a pagar', 'Ops!');
               return false;
            }

            //atribui o valor informado
            payment.valor = valor;

            //verifica as opções do pagamento
            if (payment.obs_title != null && payment.obs_title != "") {
               this.changeObs(payment, paymentSeleted)
            } else if (paymentSeleted.parcelamento == 1) {
               // this.openModalCrediario(payment, paymentSeleted);
               this.set_payment(payment);
            } else {
               this.set_payment(payment)
            }
         } else {
            this.payment = "";
         }

      });

   }

   changeObs(payment, paymentSelect) {
      this.message.swal.fire({
         title: `${payment.obs_title}`,
         input: 'text',
         inputValue: payment.obs,
         inputAttributes: {
            autocapitalize: 'off',
            'placeholder': `informe: ${payment.obs_title}...`,
         },
         showCancelButton: true,
         cancelButtonText: 'Voltar',
         confirmButtonText: 'Confirmar',
         customClass: { popup: 'swal2-sm' }
      }).then((result) => {
         if (!result.dismiss) {
            payment.obs = result.value;

            if (paymentSelect.parcelamento == 1) {
               // this.openModalCrediario(payment, paymentSelect);
               this.set_payment(payment);
            } else {
               this.set_payment(payment);
            }
         } else {
            this.payment = "";
         }
      });
   }

   // openModalCrediario(payment, paymentSelect) {
   //    const modalRef = this.modalCtrl.open(GeraParcelasComponent, { size: 'lg', backdrop: 'static' });
   //    modalRef.componentInstance.data = payment;
   //    modalRef.result.then(resp => {
   //       if (resp != undefined) {
   //          this.set_payment(resp);
   //       }
   //    });
   // }
   set_payment(payment) {
      payment.nfe_id = this.nfe.id;
      payment.forma_id = payment.id;

      this.loadingItens = true;
      this.ref.detectChanges();
      this.service.create_payment(payment).subscribe((resp: any) => {
         this.loadingItens = false;
         this.payment = "";
         this.ref.detectChanges();
         this.getItens(this.nfe.id);
      }, erro => {
         this.loadingItens = false;
         this.ref.detectChanges();
      });
      // this.paymentSelect.push(payment);
      // console.log(this.paymentSelect);
      // this.payments_calc_resto();
   }
   remove_payment(item) {
      this.loadingItens = true;
      this.ref.detectChanges();
      this.service.delete_payment(item.id).subscribe((resp: any) => {
         this.loadingItens = false;
         this.ref.detectChanges();
         this.getItens(this.nfe.id);
      }, erro => {
         this.loadingItens = false;
         this.ref.detectChanges();
      });
      // this.paymentSelect.splice(i, 1);
      // this.payments_calc_resto();
   }

   payments_calc_resto() {
      let total = 0;
      for (let i = 0; i < this.paymentSelect.length; i++) {
         total += (isNaN(this.paymentSelect[i].valor)) ? 0 : this.paymentSelect[i].valor;
      }

      let resto: any = this.nfe.total - total;
      this.nfe.resto = resto.toFixed(2);
   }

   sum_payments() {
      let pago = 0;

      this.paymentSelect.forEach(pay => {
         pago += pay.valor;
      });

      return pago;
   }

   verificaParcelas() {
      let tem = false;
      this.paymentSelect.forEach(pay => {
         if (pay.parcelas) {
            tem = true;
         }
      });

      return tem;
   }

   del_payment_confirm() {

   }

   save() {
      this.loading = true;
      this.service.update(this.nfe, this.nfe.id).subscribe((resp: any) => {
         this.ItemSource = resp.itens;
         this.loading = false;
         this.ref.detectChanges();
         this.getDados(this.nfe.id);
      }, (erro) => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   selectEmitente() {
      const modalRef = this.modalCtrl.open(GeraNotaComponent, { size: 'md', backdrop: 'static' });
      modalRef.componentInstance.data = { modelo: 55 };
      modalRef.result.then(resp => {
         if (resp) {
            console.log(resp);
            this.nfe.emitente_id = resp.emitente_id;
            this.sendNFe();
            // this.getDados(this.nfe.id);
            // this.getItens(this.nfe.id);
         }
      });
   }

   sendNFe() {
      this.loading = true;
      this.ref.detectChanges();
      this.service.emitir(this.nfe).subscribe((resp: any) => {
         if (resp.pdf_url) {
            window.open(resp.pdf_url, '_blank');
         }
         this.getDados(this.nfe.id);
         this.loading = false;
         this.ref.detectChanges();
      }, erro => {
         this.message.alertErro(erro.error.text);
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   printNFe() {
      this.loading = true;
      this.ref.detectChanges();
      this.service.print(this.nfe.id).subscribe((resp: any) => {
         if (resp.pdf_url) {
            window.open(resp.pdf_url, '_blank');
         }
         this.loading = false;
         this.ref.detectChanges();
      }, erro => {
         this.message.alertErro(erro.error.text);
         this.loading = false;
         this.ref.detectChanges();
      });
   }


}
