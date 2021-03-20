import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {AlertController, LoadingController} from '@ionic/angular';
import {AuthService} from '../../services/auth.service';

@Component({
  selector: 'app-retrait',
  templateUrl: './retrait.page.html',
  styleUrls: ['./retrait.page.scss'],
})
export class RetraitPage implements OnInit {
  position = 'beneficiaire';
  form: FormGroup;
  visible = true;

  constructor(private fb: FormBuilder,
              private  alertCtrl: AlertController,
              private loadingCtrl: LoadingController,
              private authService: AuthService) { }

  ngOnInit() {
    this.form = this.fb.group({
      code: ['', [Validators.required, Validators.minLength(9),Validators.maxLength(9)]],
      CNI: ['', [Validators.required, Validators.minLength(13),Validators.maxLength(13)]],
    });
  }

  preview() {
    this.visible = true;
  }
  next() {
    this.visible = false;
    this.position = 'emetteur';
  }

  retirer() {

  }

  rechercher() {

    this.authService.findTransactionByCode(this.form.value).subscribe(
      (data)=>{
        console.log(data);
      },(error) => {
        console.log(error);
      }
    )
  }
}
