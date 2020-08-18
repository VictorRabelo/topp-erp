import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BusnessFormComponent } from './busness-form.component';

describe('BusnessFormComponent', () => {
  let component: BusnessFormComponent;
  let fixture: ComponentFixture<BusnessFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BusnessFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BusnessFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
