import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class PaymentService {
   public url = environment.url;

   constructor(
      public http: HttpClient
   ) { }

   getList(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/payment`, { params: queryParams });
   }

   create(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/payment`, data);
   }

   update(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/payment/${id}`, data);
   }

   getById(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/payment/${id}`);
   }

   delete(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/payment/${id}`);
   }
}