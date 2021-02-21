import { Component, ElementRef, Input, OnInit, ViewChild } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { Product } from 'src/app/tools/entities/product.entity';
import { Like } from 'typeorm';

@Component({
  selector: 'app-modal-products',
  templateUrl: './modal-products.component.html',
  styleUrls: ['./modal-products.component.scss']
})
export class ModalProductsComponent implements OnInit {

  @ViewChild('inputSearch', { static: true }) inputSearch: ElementRef

  screen = window.innerHeight;

  termo: any = "%";

  products = [];

  @Input() data: string;

  constructor(
    private activeModal: NgbActiveModal,
  ) { }

  ngOnInit() {
    this.termo = this.data;
    this.inputSearch.nativeElement.focus();
    this.getProducts();
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  async getProducts() {
    this.products = await Product.find({
      where: [
        { id: Like(`%${this.termo}%`) },
        { codigo_barras: Like(`%${this.termo}%`) },
        { referencia: Like(`%${this.termo}%`) },
        { descricao: Like(`%${this.termo}%`) }
      ], take: 100, order: { descricao: 'ASC' }
    });
  }

  open_item(item) {
    this.close(item.id);
  }

}
