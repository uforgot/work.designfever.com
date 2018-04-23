<?php
	@ini_set("session.cookie_lifetime", 0);
	@ini_set("session.cache_expire", 36000);
	session_start();

	require_once "define.php";		// ����ȭ��

	require_once "db.php";			// DB
	require_once "function.php";	// function, class

	//���� ������ȣ
	$prs_id = isset($_SESSION['DF_PRS_ID']) ? $_SESSION['DF_PRS_ID'] : null;

	//�̻��̻� ���� �迭
	$positionS_arr = array('�̻�','��ǥ');

	//�����̻� ���� �迭
	$positionA_arr = array('����','����','����','�̻�','��ǥ');

	//�������� ���� �迭(�ְ����� �ۼ�)
	$positionB_arr = array('����','���','����','����','�븮','PD','å��','����','����','����');

	//2018 �������� �ְ����� ���� ���� (�� �Ǻ� ���� ������)
	//$NoCommuting_arr = array('������','������','�ֵ���','�ڼ�õ','����ȯ');
	$NoCommuting_arr = array('22','87','148','15','24');

	//2018 �������� ���� ��/���� ����
	//$positionC_arr = array('��¾�','������','�����','������','�����','������','������','������','�ݽ���','�Ѽ���','�ѿ���','�����');
	$positionC_arr = array('308','324','60','48','59','164','191','71','95','85','80','26');

	//2018 �������� �ְ����� ���� ���� (�� �Ǻ� ���� ������)
	//$weekly_arr = array('������','�ѿ���','�����');
	$weekly_arr = array('164','80','26');

	//2018 �������� ���ڰ��� �ް� ����������
	//$approval_arr = array('��¾�','������','�����','������','�����','������','������','�ݽ���','�Ѽ���','�ѿ���','�����');
	$approval_arr = array('308','324','60','48','59','164','71','95','85','80','26');

	//�濵������ 
	//$business_arr = array('���ڿ�','�����','�����','������','�ӿ츮','������','��ΰ�','������','������','�����','������','��ȿ��');
	$happyLab_arr = array('161','151','26','172','193','190','118','128','189','112','28','197');

	//�ູ������ 
	//$happyLab_arr = array('�����','������');
	$business_arr = array('26','290');

	//��Ʈ�ʸ�� 
	$partner_arr = array('Creative Planning Division','Creative Planning 1 Team','Creative Planning 2 Team','Marketing Planning Division');

	//���� ��� IP
	//ȸ�� IP(Lexus VPN), ȸ�� IP, ���� IP
	$ok_ip_arr = array('119.192.230.238','119.192.230.239','59.10.250.106');

/*
//����
	//PRF_ID
	0 - ���δ��
	1 - ����
	2 - ����
	3 - ����		= �λ������
	4 - ������,�ӿ�	= ��ü������
	5 - ����
	6 - �����
	7 - �İ�/�����

	//GUNUN1(���),GUBUN2(���)
	1 - ���
	2 - ���
	3 - ����ٹ�
	4 - ������Ʈ ��������
	5 - ������Ʈ ���Ĺ���
	6 - �ܱ�
	7 - ����
	8 - ��������
	9 - ���Ĺ���
	10 - �ް�
	11 - ����
	12 - ������
	13 - ��Ÿ
	14 - ���,�ް�������
	15 - ����/�Ʒ�
	16 - ������Ʈ�ް�
	17 - ���������ް�
	18 - �����ް�
	19 - �ι���/����
	20 - ����ް�
	21 - ��������
*/

	// �������� ��Ī �迭
	$_team_trans_arr = array(
							"CEO"=>"CEO",
							"CP ��"=>"Creative Planning Division",
							"CP 1��"=>"Creative Planning 1 Team",
							"CP 2��"=>"Creative Planning 2 Team",
							"MP ��"=>"Marketing Planning Division",
							"Design 1��"=>"Design 1 Division",
							"Design 1�� 1��"=>"Design 1 Division 1 Team",
							"Design 2��"=>"Design 2 Division",
							"Design 2�� 1��"=>"Design 2 Division 1 Team",
							"Design 2�� 2��"=>"Design 2 Division 2 Team",
							"Motion ��"=>"Motion Division",
							"Motion 1��"=>"Motion 1 Team",
							"Art ��"=>"Art Division",
							"VID"=>"Visual Interaction Development",
							"VID 1��"=>"VID 1 Team",
							"VID 2��"=>"VID 2 Team",
							"LAB"=>"LAB",
							"BST ��"=>"Business Support Team"
						);	

	// ����̽� ���� �迭
	$_status_arr = array (
							"0"=>"�̹���", 
							"10"=>"������", 
							"11"=>"���λ��", 
							"12"=>"�뿩��", 
							"13"=>"������", 
							"20"=>"���� ���������", 
							"21"=>"���� ������", 
							"30"=>"���Ұ�(����/����)", 
							"31"=>"���", 
							"32"=>"�н�"
						);

	// ����̽� ���� �迭
	$_equip_auth_arr = array ("15", "79", "80", "26", "85", "109", "277", "290", "326");

?>