import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NfeFormComponent } from './nfe-form.component';

describe('NfeFormComponent', () => {
  let component: NfeFormComponent;
  let fixture: ComponentFixture<NfeFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NfeFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NfeFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
