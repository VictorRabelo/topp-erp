import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'empresa' })
export class Empresa extends BaseEntity {

	@PrimaryGeneratedColumn()
	id: string;

	@Column('varchar', { default: 1 })
	tipo: string;

	@Column('varchar', { nullable: true })
	razao: string;

	@Column('varchar', { nullable: true })
	fantasia: string;

	@Column('varchar', { nullable: true })
	cnpj: string;

	@Column('varchar', { nullable: true })
	telefone: string;

	@Column('varchar', { nullable: true })
	celular: string;

	@Column('varchar', { nullable: true })
	email: string;

	@Column('varchar', { nullable: true })
	cep: string;

	@Column('varchar', { nullable: true })
	logradouro: string;

	@Column('varchar', { nullable: true })
	numero: string;

	@Column('varchar', { nullable: true })
	bairro: string;

	@Column('varchar', { nullable: true })
	complemento: string;

	@Column('varchar', { nullable: true })
	cidade: string;

	@Column('varchar', { nullable: true })
	ibge: string;

	@Column('varchar', { nullable: true })
	uf: string;

	@Column('varchar', { nullable: true })
	logo: string;

	@Column('varchar', { nullable: true })
	plano: string;

	@Column('date', { nullable: true })
	licenca: string;

	@CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
	@UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}