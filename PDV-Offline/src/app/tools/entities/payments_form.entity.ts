import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'payments_form' })
export class PaymentsForm extends BaseEntity {

	@PrimaryGeneratedColumn()
	id: string;

	@Column('int', { nullable: true })
	empresa_id: string;

	@Column('varchar', { nullable: true })
	forma: string;

	@Column('int', { default: 0 })
	parcelamento: number;

	@Column('int', { default: 0 })
	max_parcelas: number;

	@Column('int', { default: 0 })
	cliente_require: number;

	@Column('int', { default: 0 })
	more: number;

	@Column('varchar', { nullable: true })
	obs: string;

	@Column('int', { default: 1 })
	status: number;

	@CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
	@UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}