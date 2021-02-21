import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'users_permissions' })
export class UserPermission extends BaseEntity {

	@PrimaryGeneratedColumn()
	id: number;

	@Column('int', { default: 0 })
	empresa_id: number;

	@Column('varchar', { nullable: true })
	descricao: number;

	@Column('int', { default: 0 })
	users: number;

	@Column('int', { default: 0 })
	create_user: number;

	@Column('int', { default: 0 })
	edit_user: number;

	@Column('int', { default: 0 })
	delete_user: number;

	@Column('int', { default: 0 })
	nivel_user: number;

	@Column('int', { default: 0 })
	products: number;

	@Column('int', { default: 0 })
	create_product: number;

	@Column('int', { default: 0 })
	edit_product: number;

	@Column('int', { default: 0 })
	delete_product: number;

	@Column('int', { default: 0 })
	clients: number;

	@Column('int', { default: 0 })
	create_client: number;

	@Column('int', { default: 0 })
	edit_client: number;

	@Column('int', { default: 0 })
	delete_client: number;

	@Column('int', { default: 0 })
	vendas: number;

	@Column('int', { default: 0 })
	create_venda: number;

	@Column('int', { default: 0 })
	edit_venda: number;

	@Column('int', { default: 0 })
	delete_venda: number;

	@Column('int', { default: 0 })
	cancel_venda: number;

	@Column('int', { default: 0 })
	finalizar_venda: number;

	@Column('int', { default: 0 })
	desconto_venda: number;

	@Column('int', { default: 0 })
	nfe: number;

	@Column('int', { default: 0 })
	create_nfe: number;

	@Column('int', { default: 0 })
	edit_nfe: number;

	@Column('int', { default: 0 })
	delete_nfe: number;

	@Column('int', { default: 0 })
	nfce: number;

	@Column('int', { default: 0 })
	create_nfce: number;

	@Column('int', { default: 0 })
	edit_nfce: number;

	@Column('int', { default: 0 })
	delete_nfce: number;

	@Column('int', { default: 0 })
	emitentes: number;

	@Column('int', { default: 0 })
	create_emitente: number;

	@Column('int', { default: 0 })
	edit_emitente: number;

	@Column('int', { default: 0 })
	delete_emitente: number;

	@Column('int', { default: 0 })
	caixa: number;

	@Column('int', { default: 0 })
	contas_receber: number;

	@Column('int', { default: 0 })
	contas_receber_create: number;

	@Column('int', { default: 0 })
	contas_receber_edit: number;

	@Column('int', { default: 0 })
	contas_receber_delete: number;

	@Column('int', { default: 0 })
	contas_receber_payment: number;

	@Column('int', { default: 0 })
	contas_pagar: number;

	@Column('int', { default: 0 })
	contas_pagar_create: number;

	@Column('int', { default: 0 })
	contas_pagar_edit: number;

	@Column('int', { default: 0 })
	contas_pagar_delete: number;

	@Column('int', { default: 0 })
	contas_pagar_payment: number;

	@Column('int', { default: 0 })
	create_caixa: number;

	@Column('int', { default: 0 })
	edit_caixa: number;

	@Column('int', { default: 0 })
	delete_caixa: number;

	@CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
	@UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}