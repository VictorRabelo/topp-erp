import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store, select } from '@ngrx/store';
import { MessageService } from '../../../../services/message.service';
import { VendaService } from '../../../../services/venda.service';
import { NFCeService } from '../../../../services/nfce.service';
import { ToolsService } from '../../../../services/tools.service';
import { AppState } from '../../../../core/reducers';
import { currentUser } from '../../../../core/auth';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ProdutoSearchComponent } from '../modais/produto-search/produto-search.component';
import { ProdutoDetalheComponent } from '../modais/produto-detalhe/produto-detalhe.component';
import { ClienteSearchComponent } from '../modais/cliente-search/cliente-search.component';
import { VendaFinishComponent } from '../modais/venda-finish/venda-finish.component';

@Component({
   selector: 'kt-venda-balcao',
   templateUrl: './venda-balcao.component.html',
   styleUrls: ['./venda-balcao.component.scss']
})
export class VendaBalcaoComponent implements OnInit {

   loading: boolean = false;
   loadingItens: boolean = false;

   permissions: any = {};

   vendaCurrent: any = {}
   cliente: any = {};
   vendedor: any = {};
   ItemSource = [];
   paymentSource = [];

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: VendaService,
      private serviceNFCe: NFCeService,
      private util: ToolsService,
      private ActiveRoute: ActivatedRoute,
      private modalCtrl: NgbModal,
      private store: Store<AppState>,
      private router: Router
   ) {
      this.getPermissions();
   }

   ngOnInit() {
      this.loading = true;
      this.ActiveRoute.params.subscribe(params => {
         if (params['id']) {
            const id = params['id'];
            this.getDados(id)
         }
         else {
            this.router.navigate(['/vendas']);
         }
      });
   }

   getPermissions() {
      this.store.pipe(select(currentUser)).subscribe((resp: any) => {
         if (resp) {
            // console.log(resp.permissions);
            this.permissions = resp.permissions;
         }
      });
   }

   getTitulo() {
      if (this.vendaCurrent.id > 0) {
         return "Venda: " + this.vendaCurrent.id;
      } else {
         return "Venda Fechada";
      }
   }

   getDados(id) {
      this.loading = true;
      this.service.getById(id).subscribe((resp: any) => {
         this.loading = false;

         this.vendaCurrent = resp;
         this.getItens(this.vendaCurrent.id);

         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   getItens(id_venda) {
      this.loadingItens = true;
      this.service.getListItens(id_venda).subscribe((resp: any) => {
         this.ItemSource = resp.itens;
         this.vendaCurrent.descontos = resp.totais.descontos + parseFloat(this.vendaCurrent.desconto);
         this.vendaCurrent.subtotal = resp.totais.subtotal
         this.vendaCurrent.total = resp.totais.subtotal - this.vendaCurrent.descontos;

         this.paymentSource = resp.payments;

         this.loadingItens = false;
         this.ref.detectChanges();
      }, erro => {
         this.loadingItens = false;
         this.ref.detectChanges();
      });
   }
   getTroco() {
      let troco = 0;
      this.paymentSource.forEach(payment => {
         troco += parseFloat(payment.valor);
      });

      troco = troco - parseFloat(this.vendaCurrent.total);

      return troco.toFixed(2);
   }

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
      modalRef.componentInstance.data = { 'item': item, 'venda': this.vendaCurrent };
      modalRef.result.then(resp => {
         if (resp) {
            this.getItens(this.vendaCurrent.id);
            // this.search_item();
         }
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
      if (this.vendaCurrent.status != 1) {
         return false;
      }

      this.loadingItens = true;
      this.ref.detectChanges();
      this.service.delete_item(item.id).subscribe((resp: any) => {
         this.getItens(this.vendaCurrent.id);
      }, erro => {
         this.loadingItens = false;
         this.ref.detectChanges();
      });
   }

   add_cliente() {
      if (this.vendaCurrent.status != 1) {
         return false;
      }

      const modalRef = this.modalCtrl.open(ClienteSearchComponent, { size: 'lg', backdrop: 'static' });
      modalRef.componentInstance.data = this.vendaCurrent;
      modalRef.result.then(resp => {
         if (resp) {
            this.getDados(this.vendaCurrent.id);
         }
      });
   }

   finish() {
      if (this.vendaCurrent.status != 1) {
         return false;
      }

      const modalRef = this.modalCtrl.open(VendaFinishComponent, { size: 'md', backdrop: 'static' });
      modalRef.componentInstance.data = this.vendaCurrent;
      modalRef.result.then(resp => {
         if (resp) {
            this.getDados(this.vendaCurrent.id);
         }
      });
   }

   gen_nfce() {
      this.loading = true;
      this.serviceNFCe.create(this.vendaCurrent).subscribe(resp => {
         this.loading = false;
         this.ref.detectChanges();
         console.log(resp);
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

}
