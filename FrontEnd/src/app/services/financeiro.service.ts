import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class FinanceiroService {
   public url = environment.url;

   constructor(
      public http: HttpClient
   ) { }

   caixaResumo(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/financeiro/caixa`, { params: queryParams });
   }

}