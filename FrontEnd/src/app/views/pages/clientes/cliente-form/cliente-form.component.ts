import { Component, OnInit, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { ClientService } from '../../../../services/client.service';
import { tap, finalize } from 'rxjs/operators';

@Component({
   selector: 'kt-cliente-form',
   templateUrl: './cliente-form.component.html',
   styleUrls: ['./cliente-form.component.scss']
})
export class ClienteFormComponent implements OnInit {

   dados: any = { tipo: 1 };

   submited = false;
   loadingCep = false;

   @Input() public data;

   constructor(
      private message: MessageService,
      private service: ClientService,
      private activeModal: NgbActiveModal,
   ) { }

   ngOnInit() {
      console.log(this.data);
      if (this.data != undefined) {
         this.dados = this.data;
         this.getDados();
      }

   }

   close(params = undefined) {
      this.activeModal.close(params);
   }

   getTitle() {
      if (this.dados.id > 0) {
         return "Alterar Cadastro";
      } else {
         return "Novo Cadastro";
      }

   }

   getDados() {
      this.service.getById(this.dados.id).subscribe(resp => {
         this.dados = resp;
      }, erro => {
         this.close();
      })
   }

   getAddress() {
      this.loadingCep = true;
      this.service.getAdress(this.dados.cep).subscribe((resp: any) => {
         this.loadingCep = false;
         console.log(resp);
         this.dados.logra = resp.logradouro;
         this.dados.numero = resp.unidade;
         this.dados.bairro = resp.bairro;
         this.dados.cidade = resp.localidade;
         this.dados.uf = resp.uf;
         this.dados.ibge = resp.ibge;
      })
   }

   onSubmit(form) {

      if (!form.valid) {
         return;
      }

      // console.log(this.dados);

      if (this.dados.id > 0) {
         this.update();
      } else {
         this.create();
      }

   }

   create() {
      this.submited = true;
      this.service.create(this.dados).subscribe(resp => {
         this.submited = false;
         // console.log(resp);
         this.close(true);
      }, (erro) => {
         this.submited = false;
      });
   }

   update() {
      this.submited = true;
      this.service.update(this.dados, this.dados.id).subscribe(resp => {
         this.submited = false;
         // console.log(resp);
         this.close(true);
      }, (erro) => {
         this.submited = false;
      });
   }

}
