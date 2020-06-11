import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class NFCeService {
   public url = environment.url;

   constructor(
      public http: HttpClient
   ) { }

   getList(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/nfce`, { params: queryParams });
   }

   create(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/nfce`, data);
   }

   update(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/nfce/${id}`, data);
   }

   getById(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/nfce/${id}`);
   }

   delete(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/nfce/${id}`);
   }
}