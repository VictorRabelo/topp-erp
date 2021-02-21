import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { environment } from "src/environments/environment";

@Injectable({
	providedIn: 'root'
})
export default class AuthService {

	url = environment.base_url;

	constructor(private http: HttpClient) { }

	loginServe(data): Promise<any> {
		return this.http.post(`${this.url}/auth/login`, data).toPromise();
	}

	receiveCarga(id: number, dados: any): Promise<any> {
		return this.http.post(`${this.url}/carga/push/${id}`, dados).toPromise();
	}
	sendCarga(id: number, dados: any): Promise<any> {
		return this.http.post(`${this.url}/carga/send/${id}`, dados).toPromise();
	}

}