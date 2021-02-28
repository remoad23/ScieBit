import {Component, Input} from '@angular/core';
import {Router} from "@angular/router";
import {SharedService} from "../../../../Services/shared.service";

@Component({
  selector: 'pathbutton',
  template:
  `<button class="pathButton" (click)="goToOtherSectionOrFolder()">{{name}}</button>
    <i class="arrowIcon rotatedArrow"></i>`,
  styles: [`
    :host{
      display: flex;
      flex-flow: row;
    }
  p{
    font-size: 1.3em;
    color: #36506C;
    font-weight: bold;
  }
  button{
    background-color: transparent;
    border: none;
    font-size: 1em;
    color: #36506C;
    font-weight: 600;
    width: auto !important;
    height: auto !important;
  }
  .pathButton{
    pointer-events: all;
  }
  .rotatedArrow
  {
    transform: rotate(270deg);
    background-position: top;
    background-size: 70%;
    pointer-events: none;
  }
  button:hover{
    cursor: pointer;
  }
  `],
})
export class PathbuttonComponent {

  @Input() url: string;
  @Input() name: string;

  constructor(private router: Router,private shared: SharedService)
  {
    this.url = "";
    this.name = "";
  }

  goToOtherSectionOrFolder()
  {
    this.shared.getObject("app").fileOrFolderSelected = false;
    this.shared.removeObject("selectedFile");
    this.shared.removeObject("selectedFolder");
    if(this.router.url.includes('my/'))
    {
      this.router.navigate(['my']).then(() =>{
        this.router.navigate([ this.url],{ replaceUrl: true });
      });
    }
    else if(this.router.url.includes('shared/'))
    {
      this.router.navigate(['shared']).then(() =>{
        this.router.navigate([ this.url],{ replaceUrl: true });
      });
    }
    else if(this.router.url.includes('department/'))
    {
      this.router.navigate(['department']).then(() =>{
        this.router.navigate([this.url],{ replaceUrl: true });
      });
    }
  }



}
