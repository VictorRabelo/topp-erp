import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
	providedIn: 'root'
})
export class VendaService {
	public url = environment.url;

	constructor(
		public http: HttpClient
	) { }

	getList(queryParams: any): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/venda`, { params: queryParams });
	}

	create(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/venda`, data);
	}

	update(data: any, id: number): Observable<any[]> {
		return this.http.put<any[]>(`${this.url}/venda/${id}`, data);
	}

	getById(id: number): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/venda/${id}`);
	}

	delete(id: number): Observable<any[]> {
		return this.http.delete<any[]>(`${this.url}/venda/${id}`);
	}

	finish(data: any, id: number): Observable<any[]> {
		return this.http.put<any[]>(`${this.url}/venda/${id}`, data);
	}
	cancel(id: number): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/venda/cancel/${id}`, {});
	}

	//itens
	getListItens(venda_id: number): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/venda_itens/${venda_id}`);
	}
	create_item(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/venda_itens`, data);
	}
	update_item(data: any, item_id: number): Observable<any[]> {
		return this.http.put<any[]>(`${this.url}/venda_itens/${item_id}`, data);
	}
	delete_item(item_id: number): Observable<any[]> {
		return this.http.delete<any[]>(`${this.url}/venda_itens/${item_id}`);
	}

	//cliente
	setClient(data: any, venda_id: number): Observable<any[]> {
		return this.http.put<any[]>(`${this.url}/venda/set-client/${venda_id}`, data);
	}


	//gera nota NFe
	geraNFe(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/venda/gera_nfe`, data);
	}

}
