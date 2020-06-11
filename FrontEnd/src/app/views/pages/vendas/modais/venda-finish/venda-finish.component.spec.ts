import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { VendaFinishComponent } from './venda-finish.component';

describe('VendaFinishComponent', () => {
  let component: VendaFinishComponent;
  let fixture: ComponentFixture<VendaFinishComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ VendaFinishComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(VendaFinishComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
