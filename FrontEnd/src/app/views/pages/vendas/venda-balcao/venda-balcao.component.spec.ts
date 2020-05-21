import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { VendaBalcaoComponent } from './venda-balcao.component';

describe('VendaBalcaoComponent', () => {
  let component: VendaBalcaoComponent;
  let fixture: ComponentFixture<VendaBalcaoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ VendaBalcaoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(VendaBalcaoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
