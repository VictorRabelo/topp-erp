import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class ClientService {
   public url = environment.url;

   constructor(
      public http: HttpClient, private httpBackend: HttpBackend
   ) { }

   getList(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/client`, { params: queryParams });
   }

   create(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/client`, data);
   }

   update(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/client/${id}`, data);
   }

   getById(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/client/${id}`);
   }

   delete(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/client/${id}`);
   }

   //consulta cnpj: https://www.receitaws.com.br/v1/cnpj/12750852000185
   getAdress(cep: number) {
      const http = new HttpClient(this.httpBackend);
      return http.get(`https://viacep.com.br/ws/${cep}/json`);
   }
}