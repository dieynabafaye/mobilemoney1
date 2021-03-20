import { Injectable } from '@angular/core';
import {CanLoad, Route, UrlSegment, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router} from '@angular/router';
import { Observable } from 'rxjs';
import {AuthService} from '../services/auth.service';
import {filter, map, take} from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanLoad {
  constructor(private authService: AuthService, private router: Router){

  }
  canLoad(): Observable<boolean>{
    return this.authService.isAuthenticated.pipe(
      filter(val => val !== null),
      take(1),
      map(isAuthenticated => {
        if (isAuthenticated){
          return true;
        }else{
          this.router.navigateByUrl('/login');
          return false;
        }
      })
    );
  }
}
