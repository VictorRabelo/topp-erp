import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ProductService } from '../../../../services/product.service';

@Component({
   selector: 'kt-produto-mov-stock',
   templateUrl: './produto-mov-stock.component.html',
   styleUrls: ['./produto-mov-stock.component.scss']
})
export class ProdutoMovStockComponent implements OnInit {

   loading: boolean = false;

   produto: any = {};

   dados: any = {};

   @Input() public data;

   constructor(
      private ref: ChangeDetectorRef,
      private service: ProductService,
      private activeModal: NgbActiveModal,
   ) { }

   ngOnInit() {
      if (this.data != undefined) {
         this.produto = this.data;
         this.getDados();
      } else {
         this.close();
      }

   }

   close(params = undefined) {
      this.activeModal.close(params);
   }

   getDados() {
      this.service.getById(this.produto.id).subscribe(resp => {
         this.produto = resp;
      }, (erro) => {
         this.close();
      })
   }

   submit(form) {
      console.log(form);
      if (!form.valid) {
         return false;
      }

      let data: any = form.value;

      data.produto_id = this.produto.id;
      data.valor_unitario = this.produto.custo;

      this.loading = true;
      this.ref.detectChanges();
      this.service.mov_estoque(data).subscribe(resp => {
         this.loading = false;
         this.ref.detectChanges();
         this.close(resp);
      }, (erro) => {
         this.loading = false;
         this.ref.detectChanges();
      })
   }

}
