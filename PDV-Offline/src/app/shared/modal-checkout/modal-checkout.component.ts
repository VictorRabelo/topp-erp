import { Component, Input, OnInit } from '@angular/core';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from 'src/app/services/message.service';
import { ToolsService } from 'src/app/services/tools.service';
import { PaymentsForm } from 'src/app/tools/entities/payments_form.entity';
import { Venda } from 'src/app/tools/entities/venda.entity';
import { VendaPayments } from 'src/app/tools/entities/venda_payments.entity';
import { ModalParcelasComponent } from '../modal-parcelas/modal-parcelas.component';

@Component({
  selector: 'app-modal-checkout',
  templateUrl: './modal-checkout.component.html',
  styleUrls: ['./modal-checkout.component.scss']
})
export class ModalCheckoutComponent implements OnInit {

  screen = window.innerHeight;

  vendaCurrent: any = {}

  payments = [];
  payment = '';
  paymentSelected = [];

  @Input() data: any;


  constructor(
    private modalCtrl: NgbModal,
    private activeModal: NgbActiveModal,
    private message: MessageService,
    private tools: ToolsService
  ) { }

  async ngOnInit() {
    this.vendaCurrent = this.data;
    await this.calc_desconto();
    await this.getPayments();
    await this.calcResto();
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  async getPayments() {

    this.payments = await PaymentsForm.find({
      where: [
        { empresa_id: this.vendaCurrent.empresa_id }
      ]
    });
    console.log(this.payments);
  }

  changePayment(value) {
    console.log('value', value)
    const index = this.payments.findIndex(pay => pay.id == value);
    if (index >= 0) {
      this.addPayment(this.payments[index])
    }
  }

  addPayment(paymentForm) {
    this.message.swal.fire({
      title: `${paymentForm.forma}`,
      html: `Informe o valor a pagar.`,
      allowOutsideClick: false,
      input: 'number',
      inputValue: `${this.vendaCurrent.resto}`,
      inputAttributes: {
        'step': 'any',
      },
      customClass: { popup: 'swal2-sm' }
    }).then(res => {
      this.payment = '';
      if (res.isConfirmed && res.value.length > 0) {
        const valor = parseFloat(res.value);

        //verifica se pode inserir mais do que o valor a ser pago
        if (paymentForm.more == 0 && valor > this.vendaCurrent.resto) {
          this.message.alertErro('Pagamento acima do total a pagar', 'Ops!');
          return false;
        }

        if (paymentForm.parcelamento == 1) {
          if (paymentForm.cliente_require && !this.vendaCurrent.cliente_id) {
            this.message.alertErro('Formas de pagamento com parcelamento informe um cliente cadastrado!', 'Ops!');
            return false;
          }
        }

        let payment: any = {
          'venda_id': this.vendaCurrent.id,
          'forma_id': paymentForm.id,
          'forma': paymentForm.forma,
          'valor': valor,
          'resto': this.vendaCurrent.resto,
        }

        if (payment.obs_title != null && payment.obs_title != "") {
          this.changeObs(payment, paymentForm)
        } else if (paymentForm.parcelamento == 1) {
          this.openModalParcelas(payment, paymentForm);
        } else {
          this.set_payment(payment)
        }

      }
      console.log('paymentSelected', this.paymentSelected)
      console.log('res', res);
    })
  }

  changeObs(payment, paymentForm) {
    this.message.swal.fire({
      title: `${paymentForm.obs}`,
      input: 'text',
      inputValue: payment.obs,
      inputAttributes: {
        autocapitalize: 'off',
        'placeholder': `informe: ${paymentForm.obs}...`,
      },
      showCancelButton: true,
      cancelButtonText: 'Voltar',
      confirmButtonText: 'Confirmar',
      customClass: { popup: 'swal2-sm' }
    }).then((result) => {
      console.log(result);
      if (!result.dismiss) {
        payment.obs = result.value;

        if (paymentForm.parcelamento == 1) {
          this.openModalParcelas(payment, paymentForm);
        } else {
          this.set_payment(payment);
        }
      }
    });
  }

  openModalParcelas(payment, paymentForm) {
    const modalRef = this.modalCtrl.open(ModalParcelasComponent, { size: 'lg', backdrop: 'static' });
    modalRef.componentInstance.data = payment;
    modalRef.componentInstance.paymentForm = paymentForm;
    modalRef.result.then(resp => {
      if (resp) {
        this.set_payment(resp);
      }
    });
  }
  set_payment(payment) {
    this.paymentSelected.push(payment);
    console.log(this.paymentSelected);
    this.calcResto();
  }
  remove_payment(i) {
    this.paymentSelected.splice(i, 1);
    this.calcResto();
  }

  calcResto() {
    let totalPago = 0;
    this.paymentSelected.forEach(pay => {
      totalPago += pay.valor
    });

    const resto = this.vendaCurrent.total - totalPago;
    this.vendaCurrent.resto = resto.toFixed(2);
  }


  //calc desconto
  calc_desconto(type = 1) {
    const subtotal = this.vendaCurrent.subtotal;
    let valor = (this.vendaCurrent.desconto > 0) ? this.vendaCurrent.desconto : 0;
    let percent = (this.vendaCurrent.p_desconto > 0) ? this.vendaCurrent.p_desconto : 0;

    if (type == 1) {
      percent = (valor / subtotal) * 100;
    } else {
      valor = (percent / 100) * subtotal;
    }

    const total = subtotal - valor;

    this.vendaCurrent.desconto = valor.toFixed(2);
    this.vendaCurrent.p_desconto = percent.toFixed(2);
    this.vendaCurrent.total = total.toFixed(2);
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
    const total = subtotal - this.vendaCurrent.desconto_total;
    this.vendaCurrent.total = total.toFixed(2);

    // this.payments_calc_resto();
  }



  //finalização
  async finishSale() {
    console.log('payments', this.paymentSelected);
    console.log('vendaCurrent', this.vendaCurrent);

    for (let payment of this.paymentSelected) {
      await VendaPayments.insert(payment).catch(async () => {
        await VendaPayments.query(`DELETE FROM vendas_payments WHERE venda_id = '${this.vendaCurrent.id}'`).catch(err => console.log('err', err));
        this.message.alertErro('Falha ao gravar pagamentos');
        return
      })
    }

    const venda = await Venda.findOne(this.vendaCurrent.id);
    venda.status = 10;
    if (!venda.save()) {
      this.message.alertErro('Falha ao finalizar venda');
      return
    }

    if (this.vendaCurrent.resto < 0) {
      const troco = parseFloat(this.vendaCurrent.resto) * -1;
      this.message.alertTroco(this.tools.money(troco));
    } else {
      this.message.alertSuccess('Venda finalizada com sucesso!');
    }

    this.close(true);
  }

}
