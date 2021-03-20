import {CanLoad, Router} from '@angular/router';
import { Observable } from 'rxjs';
export const INTRO_KEY = 'intro-seen';
import { Plugins } from '@capacitor/core';
import {Injectable} from '@angular/core';
const{ Storage } = Plugins ;

@Injectable({
  providedIn: 'root'
})
export class RedirectGuard implements CanLoad {
  constructor(private router: Router){
  }
  async canLoad(): Promise<boolean>  {
    const hasSeenIntro = await Storage.get({key: INTRO_KEY});
    if (hasSeenIntro && (hasSeenIntro.value === 'true')){
      await this.router.navigateByUrl('/login', {replaceUrl: true});
    }else{
      await this.router.navigateByUrl('/', {replaceUrl: true});
    }
    return true;
  }
}
