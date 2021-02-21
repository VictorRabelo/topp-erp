import { Component, Input, OnInit } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-modal-desconto',
  templateUrl: './modal-desconto.component.html',
  styleUrls: ['./modal-desconto.component.scss']
})
export class ModalDescontoComponent implements OnInit {

  vendaCurrent: any = {}
  dados: any = {};

  @Input() data: any;

  constructor(
    private activeModal: NgbActiveModal
  ) { }

  ngOnInit() {
    this.vendaCurrent = Object.assign({}, this.data);
    if (this.data.desconto > 0) {
      this.calc_desconto();
    }
  }

  close(params = undefined) {
    this.activeModal.close(params)
  }

  calc_desconto(type = 1) {
    const subtotal = this.vendaCurrent.subtotal;
    let valor = (this.dados.valor > 0) ? this.dados.valor : 0;
    let percent = (this.dados.percent > 0) ? this.dados.percent : 0;

    if (type == 1) {
      percent = (valor / subtotal) * 100;
    } else {
      valor = (percent / 100) * subtotal;
    }

    this.dados.valor = valor;
    this.dados.percent = percent;

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

  submit(form) {
    console.log(form.value);
    console.log(this.vendaCurrent);
    this.close(this.vendaCurrent);
  }

}
