


import { useToast } from "vue-toastification";
const toast = useToast();
const options ={
  position: "top-right",
  timeout: 2000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 1,
  showCloseButtonOnHover: true,
  hideProgressBar: true,
  closeButton: false,
  icon: true,
  rtl: false
}
class Notification{
  
  success(){toast.success("Fait avec succès!", options);}
  alert(){toast.info("Êtes-vous sûr?", options);}
  error(){toast.error("Quelque chose s'est mal passé!", options);}
  errorNet(){toast.error("Verifier votre internet!", options);}
  warning(){toast.warning("Oups faux!", options);}
  image_validation(){toast.error("Téléchargez une image de moins de 1 Mo", options);}
  session_expired(){toast.error("La session est expiré", options);}
  custom_notification(text){toast.success(text, options);}
  // custom notification
  custom_error(msg){toast.error(msg, options);}
}

export default Notification = new Notification()