import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgForm } from '@angular/forms';
import {AlertController, LoadingController} from '@ionic/angular';
import {AuthService} from '../../services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {
  login: { username: string; password: string } = {
    username: '',
    password: '',
  };
  submitted = false;

  constructor(public router: Router,
              private  alertCtrl: AlertController,
              private loadingCtrl: LoadingController,
              private authService: AuthService
              ) {}

  ngOnInit() {}

  async onLogin(form: NgForm) {
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...'
    });
    await loading.present();

    this.authService.login(form.value).subscribe(
      async (response) => {
        console.log(response);
        await loading.dismiss();
        const role = this.authService.getRole();
        this.authService.RedirectMe(role);

      }, async (res) => {
        await loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Entrez vos identifiants',
          message: res.error.error,
          buttons: ['OK']
        });
        await alert.present();
      });
    //this.router.navigateByUrl('/tabs-admin/admin-system');


  }
}
