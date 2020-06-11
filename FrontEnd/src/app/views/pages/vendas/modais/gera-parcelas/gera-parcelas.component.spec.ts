import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GeraParcelasComponent } from './gera-parcelas.component';

describe('GeraParcelasComponent', () => {
  let component: GeraParcelasComponent;
  let fixture: ComponentFixture<GeraParcelasComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GeraParcelasComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GeraParcelasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
