import { Component, OnInit } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ipcRenderer } from 'electron';
import { MessageService } from '../../services/message.service';
import { ToolsService } from '../../services/tools.service';

@Component({
  selector: 'app-modal-settings',
  templateUrl: './modal-settings.component.html',
  styleUrls: ['./modal-settings.component.scss']
})
export class ModalSettingsComponent implements OnInit {

  settings: any = {};

  printers = [];

  constructor(
    private activeModal: NgbActiveModal,
    private tools: ToolsService,
    private message: MessageService
  ) { }

  ngOnInit() {
    this.printers = ipcRenderer.sendSync('printers');
    console.log(this.printers);
    if (this.tools.getSettings() != null && this.tools.getSettings() != undefined) {
      this.settings = this.tools.getSettings();
    }
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  submit(form) {
    if (!form.valid) {
      console.log(form);
      return false;
    }
    const settings = form.value;

    this.tools.postSettings(settings);
    this.message.alertSuccess('Configurações Salvas!');
    this.close(settings);
  }

}
