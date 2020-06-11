import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EmitenteFormComponent } from './emitente-form.component';

describe('EmitenteFormComponent', () => {
  let component: EmitenteFormComponent;
  let fixture: ComponentFixture<EmitenteFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EmitenteFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EmitenteFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
