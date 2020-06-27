import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class NFeService {
   public url = environment.url;

   constructor(
      public http: HttpClient
   ) { }

   getList(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/nfe`, { params: queryParams });
   }

   create(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/nfe`, data);
   }

   update(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/nfe/${id}`, data);
   }

   getById(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/nfe/${id}`);
   }

   delete(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/nfe/${id}`);
   }

   emitir(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/nfe/emitir`, data);
   }
   print(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/nfe/print/${id}`);
   }


   getListItens(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/nfe/itens`, { params: queryParams });
   }
   create_item(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/nfe/itens`, data);
   }
   update_item(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/nfe/itens/${id}`, data);
   }
   delete_item(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/nfe/itens/${id}`);
   }


   create_payment(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/nfe/payment`, data);
   }
   delete_payment(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/nfe/payment/${id}`);
   }
}