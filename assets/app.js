// Simple helper: confirm logout link
document.addEventListener('DOMContentLoaded', ()=>{
  const logout = document.querySelector('a[href="logout.php"]');
  if(logout){
    logout.addEventListener('click', (e)=>{
      if(!confirm('Anda yakin ingin logout?')) e.preventDefault();
    });
  }
});

