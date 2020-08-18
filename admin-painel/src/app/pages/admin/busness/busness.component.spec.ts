import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BusnessComponent } from './busness.component';

describe('BusnessComponent', () => {
  let component: BusnessComponent;
  let fixture: ComponentFixture<BusnessComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BusnessComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BusnessComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
