import { Injectable } from "@angular/core";

@Injectable({
   providedIn: 'root'
})
export class ToolsService {
   screen = window.innerHeight;

   getScreen() {
      let tela = this.screen * 0.5;
      console.log(tela);
      return tela;
   }

}