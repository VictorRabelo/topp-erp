import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ProductService } from '../../../../services/product.service';
import { ToolsService } from '../../../../services/tools.service';
import { tap, finalize } from 'rxjs/operators';

@Component({
   selector: 'kt-produto-search',
   templateUrl: './produto-search.component.html',
   styleUrls: ['./produto-search.component.scss']
})
export class ProdutoSearchComponent implements OnInit {

   loading = false;

   dataSource = [];
   screen: number;

   filters: any = {};

   @Input() data;

   constructor(
      private ref: ChangeDetectorRef,
      private service: ProductService,
      private util: ToolsService,
      private modalActive: NgbActiveModal
   ) {
      this.screen = this.util.getScreen();

   }

   ngOnInit() {
      this.filters = this.data;
      this.listProdutos();
   }

   close(params = undefined) {
      this.modalActive.close(params);
   }

   listProdutos() {
      this.loading = true;
      this.service.getList(this.filters).subscribe(resp => {
         this.dataSource = resp;
         this.loading = false;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   chage(item) {

      const retorno = {
         'produto_id': item.id,
         'descricao': item.descricao,
         'quantidade': 1,
         'valor_unitario': item.preco,
         'desconto': 0
      }

      this.close(retorno);

   }

}
