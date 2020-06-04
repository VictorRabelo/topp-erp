import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class UserService {
   public url = environment.url;

   constructor(
      public http: HttpClient
   ) { }

   getList(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/user`, { params: queryParams });
   }

   create(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/user`, data);
   }

   update(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/user/${id}`, data);
   }

   getById(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/user/${id}`);
   }

   delete(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/user/${id}`);
   }


   //permissions
   getListPermissions(): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/user/permissions`);
   }
   getByIdPermissions(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/user/permissions/${id}`);
   }
   createPermissions(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/user/permissions`, data);
   }
   updatePermissions(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/user/permissions/${id}`, data);
   }
   deletePermissions(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/user/permissions/${id}`);
   }
}