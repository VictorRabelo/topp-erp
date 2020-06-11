import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { EmitenteService } from '../../../../services/emitente.service';
import { ToolsService } from '../../../../services/tools.service';
import { EmitenteFormComponent } from '../emitente-form/emitente-form.component';
import { EmitenteConfigFormComponent } from '../emitente-config-form/emitente-config-form.component';
import { EmitenteCertificateComponent } from '../emitente-certificate/emitente-certificate.component';

@Component({
   selector: 'kt-emitente-detalhe',
   templateUrl: './emitente-detalhe.component.html',
   styleUrls: ['./emitente-detalhe.component.scss']
})
export class EmitenteDetalheComponent implements OnInit {

   emitente: any = {};
   nfe: any = {};
   nfce: any = {};
   loading: boolean = false;

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: EmitenteService,
      private util: ToolsService,
      private ActiveRoute: ActivatedRoute,
      private modalCtrl: NgbModal,
      private router: Router
   ) { }

   ngOnInit() {
      this.ActiveRoute.params.subscribe(params => {
         if (params['id']) {
            const id = params['id'];
            this.getDados(id)
         }
         else {
            this.router.navigate(['/emitentes']);
         }
      });
   }

   getDados(id) {
      this.loading = true;
      this.service.getById(id).subscribe((resp: any) => {
         this.loading = false;

         this.emitente = resp.dados;
         this.nfe = resp.nfe;
         this.nfce = resp.nfce;

         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   editar() {
      const modalRef = this.modalCtrl.open(EmitenteFormComponent, { size: 'lg', backdrop: 'static' });
      modalRef.componentInstance.data = this.emitente;
      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.getDados(this.emitente.id);
         }
      });
   }

   open_config(modelo) {
      const modalRef = this.modalCtrl.open(EmitenteConfigFormComponent, { size: 'md', backdrop: 'static' });
      modalRef.componentInstance.data = modelo;
      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.getDados(this.emitente.id);
         }
      });
   }

   config_certificate() {
      const modalRef = this.modalCtrl.open(EmitenteCertificateComponent, { size: 'sm', backdrop: 'static' });
      modalRef.componentInstance.data = this.emitente;
      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.getDados(this.emitente.id);
         }
      });
   }

   delete_confirm() {
      this.message.swal.fire({
         title: 'Excluir ?',
         icon: 'question',
         html: `Desaja excluir o emitente: <br><b>${this.emitente.razao}</b> ?`,
         confirmButtonText: 'Confirmar',
         showCancelButton: true,
         cancelButtonText: 'Não',
      }).then((result) => {
         if (!result.dismiss) {
            this.delete(this.emitente);
            // console.log(result);
         }
      });
   }

   delete(item) {
      this.loading = true;
      this.service.delete(item.id).subscribe(resp => {
         this.loading = false;
         this.router.navigate(['/emitentes']);
      }, erro => {
         this.loading = false;
      });
   }

}
