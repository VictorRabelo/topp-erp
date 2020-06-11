import { Component, OnInit, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { EmitenteService } from '../../../../services/emitente.service';

@Component({
   selector: 'kt-emitente-form',
   templateUrl: './emitente-form.component.html',
   styleUrls: ['./emitente-form.component.scss']
})
export class EmitenteFormComponent implements OnInit {

   dados: any = { tipo: 1 };

   submited = false;
   loadingCep = false;

   @Input() public data;

   constructor(
      private message: MessageService,
      private service: EmitenteService,
      private activeModal: NgbActiveModal,
   ) { }

   ngOnInit() {
      if (this.data) {
         this.dados = this.data;
         this.getDados();
      }

   }

   close(params = undefined) {
      this.activeModal.close(params);
   }

   getTitle() {
      if (this.dados.id > 0) {
         return "Alterar Emitente";
      } else {
         return "Novo Emitente";
      }

   }

   getDados() {
      this.service.getById(this.dados.id).subscribe((resp: any) => {
         this.dados = resp.dados;
      }, erro => {
         this.close();
      })
   }

   getAddress() {
      this.loadingCep = true;
      this.service.getAdress(this.dados.cep).subscribe((resp: any) => {
         this.loadingCep = false;
         this.dados.logradouro = resp.logradouro;
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

      console.log(this.dados);

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
         this.close(true);
      }, (erro) => {
         this.submited = false;
      });
   }

   update() {
      this.submited = true;
      this.service.update(this.dados, this.dados.id).subscribe(resp => {
         this.submited = false;
         this.close(true);
      }, (erro) => {
         this.submited = false;
      });
   }

   onFileSelected(event) {
      const selectedFiles = <FileList>event.srcElement.files;
      const files = [];
      const names = [];
      for (let i = 0; i < selectedFiles.length; i++) {
         names.push(selectedFiles[i].name);
         this.getBase64(selectedFiles[i]).then(
            (data: string) => files.push(btoa(data))
         );
      }
      this.dados.file = files;
      this.dados.file_name = names[0];
   }
   getBase64(file) {
      return new Promise((resolve, reject) => {
         const reader = new FileReader();
         reader.readAsBinaryString(file);
         reader.onload = () => resolve(reader.result);
         reader.onerror = error => reject(error);
      });
   }

}
