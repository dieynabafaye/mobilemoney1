import { Injectable } from '@angular/core';

import { Plugins} from '@capacitor/core';
import {BehaviorSubject, from, Observable, Subject} from 'rxjs';
import {HttpClient} from '@angular/common/http';
import {map, switchMap, tap} from 'rxjs/operators';
import jwt_decode from 'jwt-decode';
import { Router } from '@angular/router';
import { environment } from 'src/environments/environment';
const { Storage } = Plugins;
const TOKEN_KEY = 'my-token';


@Injectable({
  providedIn: 'root'
})
export class AuthService {
  isAuthenticated: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(null);
  token = '';
  myToken = '';
  myRole = '';
  decoded: any;
  url = environment.apiUrl;
  private rafraisir = new Subject();
  role: string;
  constructor(private http: HttpClient, private router: Router) {
    this.loadToken();
  }

  get refresh$(): any{
    return this.rafraisir;
  }
  async loadToken(){
    const token = await Storage.get({key: TOKEN_KEY});
    if (token && token.value){
      this.isAuthenticated.next(true);
    }else{
      this.isAuthenticated.next(false);
    }
  }

  loggedIn(){
    return !! Storage.get({key: TOKEN_KEY});
  }

  login(credentials: any): Observable<any>{
    return this.http.post(`${this.url}/login_check`, credentials).pipe(
      map((data: any) => data.token),
      switchMap(token => {
        // return from(Storage.set({key: TOKEN_KEY, value: token}));
        return from(this.InfosSave(token));
      }),
      tap(_ => {
        this.isAuthenticated.next(true);
      })
    );
  }

  async InfosSave(token){
    this.myToken = token;
    const tab: any = jwt_decode(token);
    this.myRole = tab.roles[0];
    await Storage.set({key: TOKEN_KEY, value: token});
    await Storage.set({key: 'role', value: tab.roles});
    await Storage.set({key: 'username', value: tab.username});

  }
  getToken(){
    return this.myToken;
  }

  getRole(){
    return this.myRole;
  }

  async getAvatar() {
    const avatar = await Storage.get({key: 'avatar'});
    if (avatar && avatar.value) {
      this.role = avatar.value;

      return this.role;
    }
  }
  async getMyRole(){
    const token = await Storage.get({key: 'role'});
    if (token && token.value){
      this.role = token.value;

      return this.role;
    }
  }

  RedirectMe(role: string){
    if (role){
      this.router.navigateByUrl('/tabs-admin/admin-system', { replaceUrl: true});
    }else {
      this.logout();
      this.router.navigateByUrl('/login', { replaceUrl: true});
    }
  }

  logout(): Promise<void>{
    this.isAuthenticated.next(false);
    Storage.remove({key: 'role' });
    Storage.remove({key: 'telephone' });
    Storage.remove({key: 'intro-seen' });
    return Storage.remove({key: TOKEN_KEY});
  }

  calculator(montant: any): Observable<any>{
    return this.http.post(`${this.url}/admin/transactions/calculer`, montant);
  }
  deCalculator(montant: any): Observable<any>{
    return this.http.post(`${this.url}/admin/transactions/decalculer`, montant);
  }

  Transaction(data: any): Observable<any>{
    return this.http.post(`${this.url}/admin/transactions`, data).pipe(
      tap(() => {
        this.rafraisir.next();
      }));
  }

  annulerTransaction(numero: any): Observable<any>{
    return this.http.post(`${this.url}/admin/transactions/delete`, numero).pipe(
      tap(() => {
        this.rafraisir.next();
      }));
  }

  findTransactionByCode(code: string): Observable<any>{
    return this.http.post(`${this.url}/admin/transactions/code`, code);
  }

  MesTransactions(): Observable<any>{
    return this.http.get(`${this.url}/admin/transactions/user`);
  }

  getSolde(data: string= 'sal'): Observable<any>{
    return this.http.post(`${this.url}/admin/transactions/solde`, data);
  }

  AddAgence(agence: any): Observable<any>{
    return this.http.post(`${this.url}/admin/agences`, agence);
  }

  Verser(data: any): Observable<any>{
    return this.http.post(`${this.url}/admin/depots`, data).pipe(
      tap(() => {
        this.rafraisir.next();
      }));
  }

  DeleteAgence(id: number): Observable<any>{
    return this.http.delete(`${this.url}/admin/agences/${id}`).pipe(
      tap(() => {
        this.rafraisir.next();
      }));
  }

  GetAgence(): Observable<any>{
    return this.http.get<any>(`${this.url}/admin/agences`);
  }


  GetCompte(): Observable<any>{
    return this.http.get<any>(`${this.url}/admin/adminSys/comptes`);
  }

  AddUser(user: any): Observable<any>{
    return this.http.post(`${this.url}/admin/adminSys/utilisateurs`, user);
  }
  deleteUser(id: number): Observable<any>{
    return this.http.delete(`${this.url}/admin/adminSys/utilisateurs/${id}`).pipe(
      tap(() => {
        this.rafraisir.next();
      }));
  }

  GetUserNotAgence(): Observable<any>{
    return this.http.get<any>(`${this.url}/admin/adminSys/utilisateurs/users`);
  }
  GetAllUsers(): Observable<any>{
    return this.http.get<any>(`${this.url}/admin/adminSys/utilisateurs`);
  }

  GetDepot(): Observable<any>{
    return this.http.get<any>(`${this.url}/admin/depots`);
  }


  deleteDepot(id: number): Observable<any>{
    return this.http.delete<any>(`${this.url}/admin/depots/${id}`);
  }

}
