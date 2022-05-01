<?php 
	if($this->session->flashdata('success_message')==1)
	{
		echo "<div class='alert alert-success text-center'><i class='fas fa-check-circle'></i> ".$this->lang->line("Your data has been successfully stored into the database.")."</div>";
		unset($_SESSION['success_message']);
	}
	
	if($this->session->flashdata('warning_message')==1)
	{
		echo "<div class='alert alert-warning text-center'><i class='fas fa-warning'></i> ".$this->lang->line("Something went wrong, please try again.")."</div>";
		unset($_SESSION['warning_message']);
	}

	if($this->session->flashdata('error_message')==1)
	{
		echo "<div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$this->lang->line("Your data was failed to stored into the database.")."</div>";
		unset($_SESSION['error_message']);
	}
		
	if($this->session->flashdata('delete_success_message')==1 || $this->session->flashdata('delete_success')==1)
	{
		echo "<div class='alert alert-success text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Your data has been successfully deleted from the database.")."</div>";
		unset($_SESSION['delete_success']);
		unset($_SESSION['delete_success_message']);
	}
	
	if($this->session->flashdata('delete_error_message')==1)
	{
		echo "<div class='alert alert-danger text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Your was failed to delete from the database.")."</div>";	
		unset($_SESSION['delete_error_message']);
	}

	if($this->session->userdata('payment_cancel')==1)
	{
		echo "<div class='alert alert-warning text-center'><i class='fa fa-remove'></i> ".$this->lang->line("Payment has been cancelled.")."</div>";
		$this->session->unset_userdata('payment_cancel');
	}

	if($this->session->userdata('payment_success')==1)
	{
		echo "<div class='alert alert-success text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Payment has been processed successfully. You may need a logout to affect subscription changes. It may take few minutes to appear payment in this list.")."</div>";
		$this->session->unset_userdata('payment_success');
	}
?>