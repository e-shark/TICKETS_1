<?php
return [
	'TicketPriority'=>[
		'EMERGENCY'=>'Срочная',
		'CONTROL1'=>'Контроль 1',
		'CONTROL2'=>'Контроль 2',
		'NORMAL'=>'-'
	],	
	'TicketStatus'=> [ 
		'1562_ASSIGN'=>'Новая 1562',
		'1562_REASSIGN'=>'Рекламация 1562',
		'1562_REFUSE'=>'Отменена 1562',
		'1562_COMPLETE'=>'Закрыта 1562',
		'KAO_COMPLETE'=>'Закрыта КАО',
		
		'DISPATCHER_ACCEPT'=>'Диспетчер: Принял в работу',
		'DISPATCHER_ASSIGN'=>'Диспетчер: Назначил исполнителя',
		'DISPATCHER_ASSIGN_MASTER'=>'Диспетчер: Передал мастеру',
		'DISPATCHER_REASSIGN'=>'Диспетчер: Назначил повторно',
		'DISPATCHER_ASSIGN_DATE'=>'Диспетчер: Установил срок заявки',
		'DISPATCHER_COMPLETE'=>'Закрыта диспетчером',
		'DISPATCHER_REFUSE'=>'Отклонена диспетчером',

		'OPERATOR_ASSIGN'=>'Оператор Н.Техн.: Назначил исполнителя',
		'OPERATOR_COMPLETE'=>'Оператор Н.Техн.: Выполнено',

		'MASTER_REFUSE'=>'Отклонена мастером',
		'MASTER_ACCEPT'=>'Мастер: Принял в работу',
		'MASTER_ASSIGN'=>'Мастер: Назначил исполнителя',
		'MASTER_REASSIGN'=>'Мастер: Назначил повторно',
		'MASTER_ASSIGN_DATE'=>'Мастер: Перенос срока заявки',
		'MASTER_COMPLETE'=>'Мастер: Выполнено',
		
		'EXECUTANT_REFUSE'=>'Отклонена исполнителем',
		'EXECUTANT_ACCEPT'=>'Принята исполнителем',
		'EXECUTANT_COMPLETE'=>'Исполнитель: выполнено',

		//--- HIDDEN Statuses - will not be included in select-list by Report_Titotals::getStatusesList(). Used mainly in ticketlog.tilstatus
		'MASTER_MESSAGE_HIDDEN'=>'Мастер:',
		'DISPATCHER_MESSAGE_HIDDEN'=>'Диспетчер:',
		'EXECUTANT_MESSAGE_HIDDEN'=>'Исполнитель:',

		'MASTER_OOSBEGIN_HIDDEN'=>'Мастер:Вывел в ОСТАНОВ',
		'DISPATCHER_OOSBEGIN_HIDDEN'=>'Диспетчер:Вывел в ОСТАНОВ',
		'MASTER_OOSEND_HIDDEN'=>'Мастер:Вывел из ОСТАНОВА',
		'DISPATCHER_OOSEND_HIDDEN'=>'Диспетчер:Вывел из ОСТАНОВА',
		'MASTER_OOSREFUSE_HIDDEN'=>'Мастер:Отменил ОСТАНОВ',
		'DISPATCHER_OOSREFUSE_HIDDEN'=>'Диспетчер:Отменил ОСТАНОВ',
		'DISPATCHER_OOSEDIT_HIDDEN'=>'Диспетчер:Изм.данные о простое',
		'MASTER_OOSEDIT_HIDDEN'=>'Мастер:Изм.данные о простое',
		'DISPATCHER_OOSSETTYPE_HIDDEN'=>'Диспетчер:Указал причину отказа',
		'MASTER_OOSSETTYPE_HIDDEN'=>'Мастер:Указал причину отказа',
		'DISPATCHER_OOSRESETTYPE_HIDDEN'=>'Диспетчер:Сбросил причину отказа',
		'MASTER_OOSRESETTYPE_HIDDEN'=>'Мастер:Сбросил причину отказа'
	],
	'TicketLogStatus'=>[
		'WORKORDER'=>'Мастер: Наряд выдан',
		'WORKORDERRECL'=>'Мастер: Рекламация',
		'WORKORDERACCEPT'=>'Мастер: Наряд закрыт',
		'JOBDONE'=>'Исполнитель: работа выполнена '
	],
	'MeterAccauntingPeriodDayOfMonth' => 10,
];
