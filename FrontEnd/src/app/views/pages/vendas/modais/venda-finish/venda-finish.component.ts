import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToolsService } from '../../../../../services/tools.service';
import { VendaService } from '../../../../../services/venda.service';
import { PaymentService } from '../../../../../services/payment.service';
import { MessageService } from '../../../../../services/message.service';
import { GeraParcelasComponent } from '../gera-parcelas/gera-parcelas.component';

@Component({
   selector: 'kt-venda-finish',
   templateUrl: './venda-finish.component.html',
   styleUrls: ['./venda-finish.component.scss']
})
export class VendaFinishComponent implements OnInit {

   loading = false;

   vendaCurrent: any = {};
   paymentsList = [];
   payment: string = '';
   paymentsCurrent = [];
   screen: number;

   @Input() data;

   constructor(
      private ref: ChangeDetectorRef,
      private service: VendaService,
      private servicePayment: PaymentService,
      private util: ToolsService,
      private message: MessageService,
      private modalActive: NgbActiveModal,
      private modalCtrl: NgbModal
   ) {
      this.screen = this.util.getScreen();
      this.getPayments();
   }

   ngOnInit() {
      if (this.data.id) {
         this.vendaCurrent = this.data;
      }
      console.log(this.vendaCurrent);
      this.calc_desconto();
   }

   close(params = undefined) {
      this.modalActive.close(params);
   }

   getPayments() {
      this.loading = true;
      this.servicePayment.getList({}).subscribe(resp => {
         this.paymentsList = resp;
         this.loading = false;
         this.ref.detectChanges();
      }, erro => {
         this.close();
      });
   }

   submit() {
      if (this.paymentsCurrent.length <= 0) {
         this.message.alertErro('Você não informou nenhum pagamento!', 'Ops!');
         return false;
      }

      let pago = this.sum_payments();
      let total = this.vendaCurrent.total.toFixed(2);
      if (pago < total) {
         this.message.alertErro('Total pago não é suficiente!', 'Ops!');
         return false;
      }

      if (this.verificaParcelas() && !this.vendaCurrent.cliente_id) {
         this.message.alertErro('Informe um cliente cadastrado!', 'Ops!');
         return false;
      }

      let request = {
         'vendaCurrent': this.vendaCurrent,
         'paymentsCurrent': this.paymentsCurrent
      }

      this.loading = true;
      this.service.update(request, this.vendaCurrent.id).subscribe((data) => {
         this.loading = false;
         this.close(true);
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   calc_desconto(type = 1) {
      const subtotal = this.vendaCurrent.subtotal;
      let vdesc = (this.vendaCurrent.desconto_b > 0) ? this.vendaCurrent.desconto_b : 0;
      let descp = (this.vendaCurrent.descontop_b > 0) ? this.vendaCurrent.descontop_b : 0;

      if (type == 1) {
         descp = (vdesc / subtotal) * 100;
      } else {
         vdesc = (descp / 100) * subtotal;
      }

      this.vendaCurrent.desconto_b = vdesc;
      this.vendaCurrent.descontop_b = descp;

      this.verifica_desconto(subtotal, descp, vdesc);
   }

   verifica_desconto(subtotal, desc, vdesc) {
      //verifica a % de descontos dos itens em relação ao subtotal
      let desc_total = (this.vendaCurrent.descontos / subtotal) * 100;
      desc_total = desc_total + desc;
      desc_total = parseFloat(desc_total.toFixed(2));

      this.vendaCurrent.descontop_total = desc_total;

      this.set_desconto_venda(subtotal, desc, vdesc);
   }

   set_desconto_venda(subtotal, desc, vdesc) {
      this.vendaCurrent.descontop_b = desc;
      this.vendaCurrent.desconto_b = vdesc;
      this.vendaCurrent.desconto_total = vdesc + this.vendaCurrent.descontos;
      this.vendaCurrent.total = subtotal - this.vendaCurrent.desconto_total;

      this.payments_calc_resto();
   }


   change_payment(ev) {
      const id_pay = ev.target.value;

      const indice = this.paymentsList.findIndex(pay => pay.id == id_pay);

      const paymentSeleted = this.paymentsList[indice];

      const payment = {
         'id': paymentSeleted.id,
         'forma': paymentSeleted.forma,
         'max_parcelas': paymentSeleted.max_parcelas,
         'obs_title': paymentSeleted.obs,
         'valor': 0
      }

      this.message.swal.fire({
         title: payment.forma,
         html: `Falta pagar: R$ ${this.util.money(this.vendaCurrent.resto)}`,
         input: 'number',
         inputValue: this.vendaCurrent.resto,
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
            if (paymentSeleted.more == 0 && valor > this.vendaCurrent.resto) {
               this.message.alertErro('Pagamento acima do total a pagar', 'Ops!');
               return false;
            }

            //atribui o valor informado
            payment.valor = valor;

            //verifica as opções do pagamento
            if (payment.obs_title != null && payment.obs_title != "") {
               this.changeObs(payment, paymentSeleted)
            } else if (paymentSeleted.parcelamento == 1) {
               this.openModalCrediario(payment, paymentSeleted);
            } else {
               this.set_payment(payment)
            }
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
         console.log(result);
         if (!result.dismiss) {
            payment.obs = result.value;

            if (paymentSelect.parcelamento == 1) {
               this.openModalCrediario(payment, paymentSelect);
            } else {
               this.set_payment(payment);
            }
         }
      });
   }

   openModalCrediario(payment, paymentSelect) {
      const modalRef = this.modalCtrl.open(GeraParcelasComponent, { size: 'lg', backdrop: 'static' });
      modalRef.componentInstance.data = payment;
      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.set_payment(resp);
         }
      });
   }
   set_payment(payment) {
      this.paymentsCurrent.push(payment);
      console.log(this.paymentsCurrent);
      this.payments_calc_resto();
   }
   remove_payment(i) {
      this.paymentsCurrent.splice(i, 1);
      this.payments_calc_resto();
   }

   payments_calc_resto() {
      let total = 0;
      for (let i = 0; i < this.paymentsCurrent.length; i++) {
         total += (isNaN(this.paymentsCurrent[i].valor)) ? 0 : this.paymentsCurrent[i].valor;
      }

      let resto: any = this.vendaCurrent.total - total;
      this.vendaCurrent.resto = resto.toFixed(2);
   }

   sum_payments() {
      let pago = 0;

      this.paymentsCurrent.forEach(pay => {
         pago += pay.valor;
      });

      return pago;
   }

   verificaParcelas() {
      let tem = false;
      this.paymentsCurrent.forEach(pay => {
         if (pay.parcelas) {
            tem = true;
         }
      });

      return tem;
   }
}
