import { Component, OnInit, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { EmitenteService } from '../../../../services/emitente.service';

@Component({
   selector: 'kt-emitente-certificate',
   templateUrl: './emitente-certificate.component.html',
   styleUrls: ['./emitente-certificate.component.scss']
})
export class EmitenteCertificateComponent implements OnInit {

   certificate: any = {};

   loading = false;

   @Input() public data;

   constructor(
      private message: MessageService,
      private service: EmitenteService,
      private activeModal: NgbActiveModal,
   ) { }

   ngOnInit() {
      if (this.data != undefined) {
         this.certificate = this.data;
      }

   }

   close(params = undefined) {
      this.activeModal.close(params);
   }

   update() {
      this.loading = true;
      this.service.updateCertificate(this.certificate, this.certificate.id).subscribe(resp => {
         this.loading = false;
         this.close(true);
      }, (erro) => {
         this.loading = false;
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
      this.certificate.file = files;
      this.certificate.file_name = names[0];
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
